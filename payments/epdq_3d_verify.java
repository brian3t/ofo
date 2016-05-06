import com.arcot.xfms.*;
import com.arcot.xfms.XFMS_Java_API.*;
import java.net.*;
import java.io.*;
import java.util.*;

public class epdq_3d_verify {

	public static void main(String args[])
	{
		//Map paymentData = new HashMap();
		Map<String, String> paymentData = new HashMap<String, String>();
		int parametersCount = args.length;
		String data = "";
		String charset = "";

		// read parameters passed to function
		if (parametersCount == 0) {
			System.out.print("Error=Error while calling function epdq_3d_verify, please use the following example: epdq_3d_verify -d YOUR_DATA -c utf-8");
			return;
		} else {
			for (int p=0; p < parametersCount; p++) {
				if (args[p].equalsIgnoreCase("-d") && (p + 1) < parametersCount) {
					p++; data = args[p];
				} else if (args[p].equalsIgnoreCase("-c") && (p + 1) < parametersCount) {
					p++; charset = args[p];
				} 
			}
		}

		// check if necessary parameters were passed correctly
		if (data.length() == 0 || charset.length() == 0) {
			System.out.print("Error=Error while calling function epdq_3d_verify, please use the following example: epdq_3d_verify -d YOUR_DATA -c utf-8");
			return;
		} 

		// read all payment data into HashMap
    try {
			String paramName, paramValue;
			String[] pairs = data.split("&");
			for (int i=0; i < pairs.length; i++) {
				if (pairs[i].length() > 0) {
					String[] fields = pairs[i].split("=", 2);
					paramName = URLDecoder.decode(fields[0], charset);
					if (fields.length == 2) {
						paramValue = URLDecoder.decode(fields[1], charset);
					} else {
						paramValue = "";
					}
					paymentData.put(paramName, paramValue);
				}
			}
		} catch (UnsupportedEncodingException e) {
			System.out.print("Error=" + e.getMessage());
			return;
    }

		// Initialize Merchant Service Provider
		XFMS_Java_API ms = null;
		try {
			// Merchant Service connection information
			String host = "www.vbv2bmshost.co.uk";
			int port = 9707;
			String transport = "ssl";
			int sockTimeout = 35;
			int connTimeout = 35;
			int maxConns = 10;
			int minConns = 1;
			String trustedCACertFile = "ssl/ServerRootCA.pem";
			String clientCertFile = "ssl/ClientCert.pem";
			String clientKeyFile = "ssl/ClientKey.pem";
			// check connection data in the hash parameters
			if (paymentData.containsKey("MSHostName")) {
				host = paymentData.get("MSHostName");
			}
			if (paymentData.containsKey("MSPort")) {
				port = Integer.parseInt(paymentData.get("MSPort"));
			}
			if (paymentData.containsKey("MSTransport")) {
				transport = paymentData.get("MSTransport");
			}
			if (paymentData.containsKey("MSSockTimeout")) {
				sockTimeout = Integer.parseInt(paymentData.get("MSSockTimeout"));
			}
			if (paymentData.containsKey("MSConnTimeout")) {
				connTimeout = Integer.parseInt(paymentData.get("MSConnTimeout"));
			}
			if (paymentData.containsKey("MSMaxConn")) {
				maxConns = Integer.parseInt(paymentData.get("MSMaxConn"));
			}
			if (paymentData.containsKey("MSMinConn")) {
				minConns = Integer.parseInt(paymentData.get("MSMinConn"));
			}
			if (paymentData.containsKey("MSSSLCACert1")) {
				trustedCACertFile = paymentData.get("MSSSLCACert1");
			}
			if (paymentData.containsKey("MSSSLClientCert")) {
				clientCertFile = paymentData.get("MSSSLClientCert");
			}
			if (paymentData.containsKey("MSSSLClientKey")) {
				clientKeyFile = paymentData.get("MSSSLClientKey");
			}

			ServerInfo si = new ServerInfo(host, port, transport, sockTimeout, connTimeout, maxConns, minConns, trustedCACertFile, clientCertFile, clientKeyFile);
			ms = XFMSFactory.getInstance(si);

		} catch (ErrorDetail errDetail) {
			System.out.print("Error=Error while calling getInstance() - ");
			System.out.print(errDetail.errMsg);
			System.out.print(" (" + errDetail.errNum + ")");
			return;
		}

		// Get the response message from the PaRes field
		String pares = "";
		if (paymentData.containsKey("PaRes")) {
			pares = paymentData.get("PaRes");
		}
		// Get credit card number from the form
		String cardNumber = "";
		if (paymentData.containsKey("CardNumber")) {
			cardNumber = paymentData.get("CardNumber"); 
		}

		AuthValidationResultEx avresult = null;
		try {
			avresult = ms.verifyAndUnpackPAResMsgEx(pares, cardNumber, cardNumber);
		} catch (ErrorDetail errDetail) {
			System.out.print("Error=Error in AuthValidationResult - ");
			System.out.print(errDetail.errMsg);
			System.out.print(" (" + errDetail.errNum + ")");
			return;
		}

		String responseData = "";

		int sigResult = avresult.signatureCheckResult;
		String statusMsg = avresult.authenticationStatusMsg;

		try {
			responseData  = "signatureCheckResult=" + sigResult;
			responseData += "&authenticationStatusMsg=" + URLEncoder.encode(statusMsg, charset);
			responseData += "&authenticationResult=" + avresult.authenticationResult;
			responseData += "&purchaseAmount=" + URLEncoder.encode(avresult.purchaseAmount, charset);
			responseData += "&XID=" + URLEncoder.encode(avresult.XID, charset);
			responseData += "&ECI=" + URLEncoder.encode(avresult.ECI, charset);
			responseData += "&ACSVerificationID=" + URLEncoder.encode(avresult.ACSVerificationID, charset);

		} catch (UnsupportedEncodingException e) {
			System.out.print("Error=" + e.getMessage());
			return;
		}

		System.out.print(responseData);
		return;

	}
}

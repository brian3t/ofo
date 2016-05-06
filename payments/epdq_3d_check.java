import com.arcot.xfms.*;
import com.arcot.xfms.XFMS_Java_API.*;
import java.net.*;
import java.io.*;
import java.util.*;
//import java.util.HashMap;
//java.lang.Object
//java.net.URLDecoder
//ArrayOutOfBoundsException
//String defaultCharset = "iso-8859-1";

public class epdq_3d_check {

	public static void main(String args[])
	{
		//Map paymentData = new HashMap();
		Map<String, String> paymentData = new HashMap<String, String>();
		int parametersCount = args.length;
		String data = "";
		String charset = "";

		// read parameters passed to function
		if (parametersCount == 0) {
			System.out.print("Error=Error while calling function epdq_3d_check, please use the following example: epdq_3d_check -d YOUR_DATA -c utf-8");
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
			System.out.print("Error=Error while calling function epdq_3d_check, please use the following example: epdq_3d_check -d YOUR_DATA -c utf-8");
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

			//ms = XFMSFactory.getConfiguredInstance();
			ServerInfo si = new ServerInfo(host, port, transport, sockTimeout, connTimeout, maxConns, minConns, trustedCACertFile, clientCertFile, clientKeyFile);
			ms = XFMSFactory.getInstance(si);

		} catch (ErrorDetail errDetail) {
			System.out.print("Error=Error while calling getInstance() - ");
			System.out.print(errDetail.errMsg);
			System.out.print(" (" + errDetail.errNum + ")");
			return;
		}

		// Create PurchaseInfo object 
		PurchaseInfo pi = new PurchaseInfo();

		// Get data such as purchase amount and card expiry date
		if (paymentData.containsKey("PurchaseAmount")) {
			pi.purchaseAmount = paymentData.get("PurchaseAmount");
		}
		// Card's expiration date formatted YYMM
		if (paymentData.containsKey("CardExpiryDate")) { 
			pi.cardExpiryDate = paymentData.get("CardExpiryDate");
		}
		// Get the other purchase info data
		if (paymentData.containsKey("PurchaseCurrency")) {
			pi.purchaseCurrency = paymentData.get("PurchaseCurrency"); 
		} else {
			pi.purchaseCurrency = "840"; // USD by default
		}
		if (paymentData.containsKey("PurchaseCurrencyExponent")) {
			pi.purchaseCurrencyExponent = paymentData.get("PurchaseCurrencyExponent"); 
		} else {
			pi.purchaseCurrencyExponent = "2"; // two decimal points by default
		}
		// Merchant Service uses system date if the purchase date is not provided "YYYYMMDD HH:MM:SS".
		if (paymentData.containsKey("PurchaseDate")) {
			pi.purchaseDate= paymentData.get("PurchaseDate");
		}

		if (paymentData.containsKey("MerchantName")) {
			pi.merchantName = paymentData.get("MerchantName"); 
		}
		if (paymentData.containsKey("MerchantID")) {
			pi.merchantID = paymentData.get("MerchantID"); 
		}
		if (paymentData.containsKey("MerchantCountryCode")) {
			pi.merchantCountryCode = paymentData.get("MerchantCountryCode"); 
			//pi.merchantCountryCode ="840"; //USA
		}
		if (paymentData.containsKey("MerchantUrl")) {
			pi.merchantUrl = paymentData.get("MerchantUrl"); 
		}

		// Optional purchase info
		if (paymentData.containsKey("PurchaseRecurringFrequency")) {
			pi.purchaseRecurringFrequency = paymentData.get("PurchaseRecurringFrequency"); 
		}
		if (paymentData.containsKey("PurchaseRecurringExpiry")) {
			pi.purchaseRecurringExpiry = paymentData.get("PurchaseRecurringExpiry"); 
		}
		if (paymentData.containsKey("PurchaseInstallment")) {
			pi.purchaseInstallment = paymentData.get("PurchaseInstallment"); 
		}
		if (paymentData.containsKey("PurchaseDescription")) {
			pi.purchaseDescription = paymentData.get("PurchaseDescription"); 
		}

		// Create the QualifyingInfo object
		QualifyingInfo qi = new QualifyingInfo();

		// Get the httpAccept and httpUserAgent headers
		if (paymentData.containsKey("HTTPAccept")) {
			qi.httpAccept = paymentData.get("HTTPAccept"); 
		}
		if (paymentData.containsKey("HTTPUserAgent")) {
			qi.httpUserAgent = paymentData.get("HTTPUserAgent"); 
		}

		// Device category: "1" means mobile device. "0" means PC (default).
		if (paymentData.containsKey("DeviceCategory")) {
			qi.deviceCategory = paymentData.get("DeviceCategory"); 
		} else {
			qi.deviceCategory = "0";
		}

		// If acquirerBIN is not given, Merchant Service uses the default from its config file
		if (paymentData.containsKey("AcquirerBIN")) {
			qi.acquirerBIN = paymentData.get("AcquirerBIN"); 
		}
		// If dsLogin and dsPassword are not given, Merchant Service uses defaults from its config file.
		// dsLoginID is the same as merchantId in the purchaseInfo
		if (paymentData.containsKey("DSLoginID")) {
			qi.dsLoginID = paymentData.get("DSLoginID"); 
		}
		if (paymentData.containsKey("DSPassword")) {
			qi.dsPassword = paymentData.get("DSPassword"); 
		}
		// Get credit card number from the form
		String cardNumber = "";
		if (paymentData.containsKey("CardNumber")) {
			cardNumber = paymentData.get("CardNumber"); 
		}
		String responseData = "";

		try {
			// If authentication is required, create the PAReq.
			AuthRequiredCreatePAReqResult arcparr = ms.getPAReqIfAuthReqEx(cardNumber, qi, pi);
			AuthRequiredResult arr = arcparr.authRequiredResult;
			int authReqVal = arr.authRequired;
			responseData = "AuthRequired=" + authReqVal;
			if (authReqVal != 0) { 
				// Authentication IS required
				try {
					// Get the URL to contact for authentication
					String acsUrl = arr.ACSUrl;
					responseData += "&ACSUrl=" + URLEncoder.encode(acsUrl, charset);
					// Get the PAReq message
					CreatePAReqResult cprr = arcparr.createPAReqResult;
					String paReqMsg = cprr.PAReqMsg;
					String xID = cprr.XID; // yoda
					responseData += "&PAReq=" + URLEncoder.encode(paReqMsg, charset);
					responseData += "&XID=" + URLEncoder.encode(xID, charset);
				} catch (UnsupportedEncodingException e) {
					System.out.print("Error=" + e.getMessage());
					return;
				}
				/*
				// If the device is a mobile device, store the IssuerCert and the PAReqMsg
				// they will be needed for unpacking the bank's response message.
				if (authReqVal==2) {
					// these are functions you define to save session info
					storeIssuerCert(arcparr.authRequiredResult.issuerCert);
				}//*/
			}
			// Output response 
			System.out.print(responseData);
			return;

		} catch (ErrorDetail errDetail) {
			System.out.print("Error=Error while calling getPAReqIfAuthReqEx() - ");
			System.out.print(errDetail.errMsg);
			System.out.print(" (" + errDetail.errNum + ")");
			return;
		}

	}
}

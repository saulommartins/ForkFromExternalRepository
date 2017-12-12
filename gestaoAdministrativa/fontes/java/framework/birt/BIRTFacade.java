/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
package br.org.cnm.birt;

import java.io.File;
import java.util.HashMap;
import java.util.Map;
import java.util.logging.Level;

import org.eclipse.birt.core.exception.BirtException;
import org.eclipse.birt.core.framework.Platform;
import org.eclipse.birt.report.engine.api.EngineConfig;
import org.eclipse.birt.report.engine.api.EngineConstants;
import org.eclipse.birt.report.engine.api.HTMLActionHandler;
import org.eclipse.birt.report.engine.api.HTMLEmitterConfig;
import org.eclipse.birt.report.engine.api.HTMLRenderContext;
import org.eclipse.birt.report.engine.api.HTMLRenderOption;
import org.eclipse.birt.report.engine.api.HTMLServerImageHandler;
import org.eclipse.birt.report.engine.api.IReportEngine;
import org.eclipse.birt.report.engine.api.IReportEngineFactory;
import org.eclipse.birt.report.engine.api.IReportRunnable;
import org.eclipse.birt.report.engine.api.IRunAndRenderTask;

/**
 * Allows report rendering outside BIRT's report viewer.
 * 
 * @author Luiz Geovani Vier - E-Core Desenvolvimento de Software
 */
public class BIRTFacade {
	
	private static int accessCount = 0;
	private static IReportEngine engine = null;
	private static EngineConfig config = null;
	
	private static String baseURL;
	private static String baseImageURL;
	private static String imageFolder;
	
	/**
	 * Environment setup
	 * <br/>Setup is performed only on the first request
	 * @param engineHome
	 * @param logFolder
	 * @param baseURL
	 * @param baseImageURL
	 * @param imageFolder
	 * @return
	 * @throws BirtException
	 */
	public int setup(String engineHome, String logFolder, String baseURL, String baseImageURL, String imageFolder) throws BirtException {
		
		if (config == null) {
			synchronized (BIRTFacade.class) {
				if (config == null) {
					
					BIRTFacade.baseURL = baseURL;
					BIRTFacade.baseImageURL = baseImageURL;
					BIRTFacade.imageFolder = imageFolder;
					
					// Configure the Engine and start the Platform
					config = new EngineConfig();
					
					config.setEngineHome(engineHome);
					// set log config using ( null, Level ) if you do not want a log
					// file
					config.setLogConfig(logFolder, Level.FINE);
			
					Platform.startup(config);
					IReportEngineFactory factory = (IReportEngineFactory) Platform.createFactoryObject(IReportEngineFactory.EXTENSION_REPORT_ENGINE_FACTORY);
					engine = factory.createReportEngine(config);
					engine.changeLogLevel(Level.WARNING);
				}
			}
		}
		return ++accessCount;
	}

	public static void shutdown() {
		engine.shutdown();
		Platform.shutdown();
	}
	
	/**
	 * Generates the report
	 * @param reportDesignPath Layout file
	 * @param parameters Parameters map
	 * @param outputFormat html, pdf, etc.
	 * @return Location of the generated report
	 * @throws Exception
	 */
	public String generateReport(String reportDesignPath, Map parameters, String outputFormat) throws Exception {
		
		
		try {
			
			// Configure the emitter to handle actions and images
			HTMLEmitterConfig emitterConfig = new HTMLEmitterConfig();
			emitterConfig.setActionHandler(new HTMLActionHandler());
			HTMLServerImageHandler imageHandler = new HTMLServerImageHandler();
			emitterConfig.setImageHandler(imageHandler);
			
			config.getEmitterConfigs().put(outputFormat, emitterConfig); //$NON-NLS-1$
	
			IReportRunnable design = null;
	
			// Open the report design
			design = engine.openReportDesign(reportDesignPath);
	
			// Create task to run and render the report,
			IRunAndRenderTask task = engine.createRunAndRenderTask(design);
			HashMap contextMap = new HashMap();
			
		
			// Set Render context to handle url and image locataions
			HTMLRenderContext renderContext = new HTMLRenderContext();
			
			// Set the Base URL for all actions
			renderContext.setBaseURL(baseURL);
			
			// Tell the Engine to prepend all images with this URL - Note this
			// requires using the HTMLServerImageHandler
			renderContext.setBaseImageURL(baseImageURL);
			
			// Tell the Engine where to write the images to
			renderContext.setImageDirectory(imageFolder);
			
			// Tell the Engine what image formats are supported. Note you must have
			// SVG in the string
			// to render charts in SVG.
			renderContext.setSupportedImageFormats("JPG;PNG;BMP;SVG");
				
			contextMap.put(EngineConstants.APPCONTEXT_HTML_RENDER_CONTEXT, renderContext);
			
			
			task.setAppContext(contextMap);
			// Set parameters for the report
			task.setParameterValues(parameters);
			task.validateParameters();
	
			// Set rendering options - such as file or stream output,
			// output format, whether it is embeddable, etc
			HTMLRenderOption options = new HTMLRenderOption();
			// Set ouptut location
			
			//ByteArrayOutputStream bos = new ByteArrayOutputStream();
			//options.setOutputStream(bos);
			
			// Create temp file
			// This file have to be removed by the caller
			File tmpFile = File.createTempFile("birt_report", "." + outputFormat);
			String tmpFilePath = tmpFile.getAbsolutePath();
			// Set ouptut location
			options.setOutputFileName(tmpFilePath);
			
			// Set output format
			options.setOutputFormat(outputFormat);
			
			task.setRenderOption(options);
	
			// run the report
			task.run();
			task.close();
			
			return tmpFilePath;
			
		} catch (Exception ex) {
			ex.printStackTrace();
			throw ex;
		}
	}
	
	/**
	 * test
	 * @param args
	 * @throws Exception
	 */
	public static void main(String[] args) throws Exception {
		Map parameters = new HashMap();
		parameters.put("name", "Fulano");
		BIRTFacade facade = new BIRTFacade();
		facade.setup("/var/www/birt-runtime-2_1_1/ReportEngine", "/tmp", "http://localhost", "http://localhost/images", "/tmp/birt/images");
		String fn = facade.generateReport("/home/domluc/downloads/BIRTStandalone/test.rptdesign", parameters, "pdf");
		System.out.println(fn);
	}
	
}
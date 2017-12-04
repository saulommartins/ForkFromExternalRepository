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
/**
 * TODO Classe para capturar MAC address da maquina cliente.
 * 
 * @author Analista: Lucas Leusin
 * @author Desenvolvedor: Anderson R. M. Buzo
 *
 * Caso de Uso: uc-02.04.18
 * 
 */
import java.io.*;
import java.util.*;
import java.util.regex.*;
 
public final class MACAddress {

    /*
     * String para Nome do sistema operacional do cliente
     */
    static private String os = System.getProperty("os.name");
    /*
     * Boolean para validar se o método está sendo executada pela segunda vez
     */
    static private boolean repetindo = false;
    /*
     * Pattern para encontrar o MAC Address da saida do system call
     */
    static private Pattern macPattern = Pattern.compile(".*((:?[0-9a-f]{2}[-:]){5}[0-9a-f]{2}).*", Pattern.CASE_INSENSITIVE);
    /*
     * Array de string para comando e parametros executados para pegar o MAC no win98
     */
    static final String[] windows98Command = {"ipconfig","/tudo"};
    /*
     * Array de string para comando e parametros executados para pegar o MAC no windows
     */
    static final String[] windowsCommand   = {"ipconfig","/all"};
    /*
     * Array de string para comando e parametros executados para pegar o MAC no linux
     */
    static final String[] linuxCommand     = {"/sbin/ifconfig","-a"};


    /**
     * Método static para pegar Mac Address da maquina cliente conforme sistema operacional
     */
    public final static String getMacAddresses() throws IOException {
     
        StringBuffer macAddressList = new StringBuffer();
        String[] command;

        if(os.startsWith("Windows")) {
    
            if( repetindo ) {
                command = windows98Command;
            } else {
                command = windowsCommand;
            }
        } else if(os.startsWith("Linux")) {
            command = linuxCommand;
        } else {
            throw new IOException("Sistema operacional desconhecido: " + os);
        }
        
        Process process = Runtime.getRuntime().exec(command);
        
        // Extai o MAC addresses da saida do system call
        BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()));
        for (String line = null; (line = reader.readLine()) != null;) {
            Matcher matcher = macPattern.matcher(line);
            if (matcher.matches()) {
                macAddressList.append(matcher.group(1));
            }
        }
        reader.close();

        String macAddress = macAddressList.toString();
        
        if( macAddress.equals( "" ) && !repetindo && os.startsWith( "Windows" ) ) {
            os = "Windows 98";
            repetindo = true;
            macAddress = getMacAddresses();
        }

        return macAddress;
    }
    
    
}

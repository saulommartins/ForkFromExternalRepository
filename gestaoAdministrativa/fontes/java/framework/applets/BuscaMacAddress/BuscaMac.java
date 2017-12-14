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
import java.applet.Applet;
import java.awt.Graphics;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.io.IOException;
import java.io.UnsupportedEncodingException;

public class BuscaMac extends Applet {

    /*
     * StringBuffer para mensagens do applet
     */
    private StringBuffer buffer;
    /*
     * Objeto String do mac address
     */
    private String macAddress;
    /*
     * Objeto String para hash md5 do mac address
     */
    private String hashMacAddress;
    /*
     * Objeto MessageDigest para criptografia do MAC ADDRESS
     */
    private MessageDigest md;

    
    /*
     * Método padrão de inicialização do applet
     */
    public void init() {
        buffer = new StringBuffer();
    }


    /* 
     * Método padrão de inicialização do applet
     */
    public void start() {
        try {
            macAddress = new MACAddress().getMacAddresses();
        } catch( IOException e ) {
            buffer.append( e.toString() ).append( '\n' );
            repaint();
        }

        try {
            if( !macAddress.equals( "" ) ) {
                md = MessageDigest.getInstance( "MD5" );
            }
        } catch( NullPointerException e ) {
            buffer.append( e.toString() );
        } catch( NoSuchAlgorithmException e ) {
            buffer.append( e.toString() );
        } finally {
            repaint();
        }

        hashMacAddress = compute( macAddress );
    }


    /**
     * * Gera o hash md5 da string s.
     * 
     * @return MD5 digest <code>String</code>
     */
    public String compute( String s ) {
        
        char[] charArray = s.toCharArray();
        byte[] byteArray = new byte[charArray.length];
                        
        for (int i=0; i<charArray.length; i++)
           byteArray[i] = (byte) charArray[i];
                                        
        StringBuffer hexValue = new StringBuffer();

        try {
            byte[] md5Bytes = md.digest(byteArray);
            
            for (int i=0; i<md5Bytes.length; i++) {
                int val = ((int) md5Bytes[i] ) & 0xff; 
                if (val < 16) hexValue.append("0");
                hexValue.append(Integer.toHexString(val));
            }
        } catch( NullPointerException e ) {
        }

        return hexValue.toString();
    }


    /*
     * Método padrão que é executado ao parar o applet
     */
    public void stop() {
        repaint();
    }


    /*
     * Método pardrão que é executado ao encerrar o applet
     */
    public void destroy() {
    }

    

    /*
     * Método padrão para desenhar o applet
     */
    public void paint( Graphics g ) {
        g.drawString(buffer.toString(), 10, 10);
    }


    /**
     * @return Hash MD5 do MAC Address da(s) Placa(s) de rede
     */
    public String getHashMacAddress() {
        return hashMacAddress;
    }

}

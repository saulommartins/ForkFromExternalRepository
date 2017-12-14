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
 * Classe para imprimir Autenticacoes localmente
 *
 * @author Analista: Lucas Leusin
 * @author Desenvolvedor: Cleisson Barboza
 * @author Desenvolvedor: Jos� Eduardo Porto
 *
 * Caso de uso: uc-2.4.15
 *
 */
import java.io.*;

import javax.print.Doc;
import javax.print.DocFlavor;
import javax.print.DocPrintJob;
import javax.print.PrintException;
import javax.print.PrintService;
import javax.print.PrintServiceLookup;
import javax.print.SimpleDoc;


public class ImprimeAutenticacao {

    private DocFlavor df = DocFlavor.SERVICE_FORMATTED.PRINTABLE;
    private PrintService[] pss;

    public String consultarImpressoraDefault() {
        PrintService ps = PrintServiceLookup.lookupDefaultPrintService();
        try {
            return ps.getName();
        }catch (Exception e){
            return "Selecione";
        }
    }
    public String[] listarImpressoras() {
        pss = PrintServiceLookup.lookupPrintServices(df, null);
        String[] impressoras = new String[pss.length];
    	    for(int i=0; i<pss.length; i++){
            impressoras[i] = pss[i].getName();
        }
        return impressoras;
    }

    /**
     * Método para imprimir a Autenticacao
     */
    public void imprimir(byte[] b, String impressora) {

        pss = PrintServiceLookup.lookupPrintServices(df, null);
        for (int i=0; i<pss.length; i++){
            PrintService ps = pss[i];

            if (ps.getName().compareTo(impressora) == 0){

                DocPrintJob dpj = ps.createPrintJob();

                InputStream stream = new ByteArrayInputStream(b);

                DocFlavor flavor = DocFlavor.INPUT_STREAM.AUTOSENSE;
                Doc doc = new SimpleDoc(stream, flavor, null);
                try {

                    dpj.print(doc, null);

                } catch (PrintException e) {
                    e.printStackTrace();
                }
            }
        }
    }

}

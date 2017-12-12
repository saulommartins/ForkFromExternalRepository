<?php
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
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

class botoesPdfLegado
{
    //Não Apagar em desenvolvimento - Jorge
    public function NEW_imprimeBotoes($sScriptXML="", $sSQL="", $sTitulo="", $sSubTitulo="", $sParametros="")
    {
        print '
        <script type="text/javascript">
            function MostraImpressao(sessao)
            {
                var inWinWid = screen.width;
                var inWinHei = screen.height;
                var inWid = 500;
                var inHei = 300;
                var inLef = parseInt(((inWinWid - inWid) / 2),10);
                var inTop = parseInt(((inWinHei - inHei) / 2),10);
                var sessaoid = sessao.substr(10,6);
                var sArq = "../../popups/popups/impressao/FMImprimir.php?"+sessao+"&boNada=true";
                var wVolta=false;
                var sAux = "wPrint"+ sessaoid +" = window.open(sArq,\'wPrint"+ sessaoid +"\',\'width="+inWid+"px,height="+inHei+"px,left="+inLef+",top="+inTop+",resizable=0,scrollbars=0\');";
                eval(sAux);
                document.write(sAux);
            }

            function ImprimirPDF(sessao)
            {
                MostraImpressao(sessao);
                document.frmPDF.action   = "../../popups/popups/impressao/FMImprimir.php?"+sessao+"&btn=2" + HojeAgora();
                document.frmPDF.sAcaoPDF.value = "imprimir";
                var sessaoid = sessao.substr(10,6);
                document.frmPDF.target = "wPrint"+ sessaoid;
                document.frmPDF.submit();
            }
        </script>
        <form name="frmPDF" action="#" method="POST">
        <input type="hidden" name="sAcaoPDF" value="">
        <input type="hidden" name="sScriptXML" value="'.$sScriptXML.'">
        <input type="hidden" name="sSQL" value="'.$sSQL.'">
        <input type="hidden" name="sTitulo" value="'.$sTitulo.'">
        <input type="hidden" name="sSubTitulo" value="'.$sSubTitulo.'">
        <input type="hidden" name="sParametros" value="'.$sParametros.'">
        </form>
        <script type="text/javascript">
            ImprimirPDF("'.Sessao::getId().'");
        </script>
        ';
    }
    public function imprimeBotoes($sScriptXML="", $sSQL="", $sTitulo="", $sSubTitulo="", $sParametros="")
    {
        print '
        <script type="text/javascript">
            function Salvar()
            {
                document.frmPDF.action   = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/pdf/relatorioPdfLegado.php?'.Sessao::getId().'&btn=1" + HojeAgora();
                document.frmPDF.sAcaoPDF.value = "salvar";
                document.frmPDF.submit();
            }
            function Imprimir()
            {
                document.frmPDF.action   = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/pdf/imprimePdfLegado.php?'.Sessao::getId().'&btn=2" + HojeAgora();
                document.frmPDF.sAcaoPDF.value = "imprimir";
                document.frmPDF.submit();
            }
            function Enviar()
            {
                document.frmPDF.action   = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/pdf/relatorioPdfLegado.php?'.Sessao::getId().'&btn=3" + HojeAgora();
                document.frmPDF.sAcaoPDF.value = "enviar";
                document.frmPDF.submit();
            }
        </script>
        <form name="frmPDF" action="#" method="POST">
        <input type="hidden" name="sAcaoPDF" value="">
        <input type="hidden" name="sScriptXML" value="'.$sScriptXML.'">
        <input type="hidden" name="sSQL" value="'.$sSQL.'">
        <input type="hidden" name="sTitulo" value="'.$sTitulo.'">
        <input type="hidden" name="sSubTitulo" value="'.$sSubTitulo.'">
        <input type="hidden" name="sParametros" value="'.$sParametros.'">
        </form>
        <table width="200">
            <tr>
            <td class="labelcenter"   title="Salvar Relatório"><a href="javascript:Salvar();" ><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a></td>
            <td class="labelcenter" title="Imprimir Relatório"><a href="javascript:Imprimir();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a></td>
            </tr>
        </table>
        ';
    }
    public function imprimeBotoesProcesso($sScriptXML="", $sSQL="", $sTitulo="", $sSubTitulo="", $sParametros="")
    {
        $anoEx              = $_REQUEST['anoExercicio'];
        $anoExSetor         = $_REQUEST['anoExercicioSetor'];
        $codProcesso        = $_REQUEST['codProcesso'];
        $codAssunto         = $_REQUEST['codAssunto'];
        $codClassificacao   = $_REQUEST['codClassif'];
        $codOrgaoVia        = $_REQUEST['codOrgao'];
        $codUnidadeVia      = $_REQUEST['codUnidade'];
        $codDepartamentoVia = $_REQUEST['codDpto'];
        $codSetorVia        = $_REQUEST['codSetor'];
        $nomContribuinte    = $_REQUEST['nomCgm'];
        $numCgmVia          = $_REQUEST['numCgm'];
        $codMasSetor        = $_REQUEST['codMasSetor'];

        print '
        <script type="text/javascript">
            function Salvar()
            {
                document.frmPDF.action =
                "imprimeReciboProcesso.php?'.Sessao::getId().'&sAnoExercicio='.$anoEx.'&anoExercicioSetor='.$anoExSetor.'&iCodProcesso='.$codProcesso.'&codAssunto='.$codAssunto.'&codClassif='.$codClassificacao.'&codOrgao='.$codOrgaoVia.'&codUnidade='.$codUnidadeVia.'&codDpto='.$codDepartamentoVia.'&codSetor='.$codSetorVia.'&nomCgm='.$nomContribuinte.'&numCgm='.$numCgmVia.'&codMasSetor='.$codMasSetor.'"
                document.frmPDF.sAcaoPDF.value = "salvar";
                document.frmPDF.submit();
            }
            function Imprimir()
            {
                document.frmPDF.action   = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/pdf/imprimePdfLegado.php?'.Sessao::getId().'&btn=2" + HojeAgora();
                document.frmPDF.sAcaoPDF.value = "imprimir";
                document.frmPDF.submit();
            }
            function Enviar()
            {
                document.frmPDF.action   = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/pdf/relatorioPdfLegado.php?'.Sessao::getId().'&btn=3" + HojeAgora();
                document.frmPDF.sAcaoPDF.value = "enviar";
                document.frmPDF.submit();
            }
        </script>
        <form name="frmPDF" action="#" method="POST">
        <input type="hidden" name="sAcaoPDF" value="">
        <input type="hidden" name="sScriptXML" value="'.$sScriptXML.'">
        <input type="hidden" name="sSQL" value="'.$sSQL.'">
        <input type="hidden" name="sTitulo" value="'.$sTitulo.'">
        <input type="hidden" name="sSubTitulo" value="'.$sSubTitulo.'">
        <input type="hidden" name="sParametros" value="'.$sParametros.'">
        </form>
        <table width="200">
            <tr>
            <td class="labelcenter"   title="Emitir segunda via de recibo de processo"><a href="javascript:Salvar();"  ><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a></td>
            <td class="labelcenter" title="Imprimir Relatório"><a href="javascript:Imprimir();"><img src="'.CAM_FW_IMAGENS.'botao_imprimir.png" border=0></a></td>
            </tr>
        </table>
        ';
    }
}

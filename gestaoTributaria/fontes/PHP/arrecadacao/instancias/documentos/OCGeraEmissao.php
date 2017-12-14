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
  * Página de Processamento
  * Data de criação : 07/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Lucas Teixeira Stephanou

    * $Id: OCGeraEmissao.php 66548 2016-09-21 13:05:07Z evandro $

  Caso de uso: uc-05.03.11
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$passo = $request->get('passo');

if ($passo == 1) {
    $stHTML .= " <html> \n";
    $stHTML .= " <head> \n";
    $stHTML .= " <script type=\"text/javascript\"> \n";
    $stHTML .= "     function executa() { \n";
    $stHTML .= "         var mensagem = \"\"; \n";
    $stHTML .= "         var erro = false; \n";
    $stHTML .= "         var f = window.parent.frames[\"telaPrincipalRelatorio\"].document.frm; \n";
    $stHTML .= "         var d = window.parent.frames[\"telaPrincipalRelatorio\"].document; \n";
    $stHTML .= "         var aux; \n";
    $stHTML .= "         window.open('".CAM_FW_POPUPS."relatorio/Concluido.php?".Sessao::getId()."','telaPrincipalRelatorio'); \n";
    $stHTML .= "         window.open('".CAM_GT_ARR_INSTANCIAS."documentos/OCGeraEmissao.php?".Sessao::getId()."&passo=2','ocultoRelatorio'); \n";
    $stHTML .= "         if (erro) alertaAviso(mensagem,\"form\",\"erro\",\"".Sessao::getId()."\"); \n";
    $stHTML .= "     } \n";
    $stHTML .= " </script> \n";
    $stHTML .= " </head> \n";
    $stHTML .= " <body onLoad=\"javascript:executa();\"> \n";
    $stHTML .= " </body> \n";
    $stHTML .= " </html> \n";
    echo $stHTML;
} elseif ($passo==2) {
    $stNomPdf = Sessao::read( 'stNomPdf' );
    
    if ($stNomPdf == 'CarneIPTUPresidenteFigueiredo.pdf') {
        $stNomeRelatorioDownload = Sessao::read('stNomeRelatorioDownload');
        echo "<script>
                window.open('".CAM_FW_TMP.$stNomeRelatorioDownload."','_parent');        
                window.close();
            </script>";
        Sessao::remove('stNomeRelatorioDownload');
    }else{
        $arq = fopen( $stNomPdf, "r" );
        $tam = filesize( $stNomPdf );
        $buffer = fread($arq,$tam);
        $boFecha = fclose($arq);    
        header("Content-Type: application/octet-stream");
        header("Content-Length: ".$tam);
        header("Content-disposition: attachment; filename=EmissaoUrbem-".date("dmYHi").".pdf");
    }

    Sessao::remove('stNomPdf');

    echo $buffer;
}

?>
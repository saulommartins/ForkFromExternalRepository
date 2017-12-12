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
* Arquivo de implementação de manutenção de documento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28839 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-28 09:36:57 -0300 (Sex, 28 Mar 2008) $

Casos de uso: uc-01.06.96
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.96');
?>
 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) != 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
if (!(isset($_REQUEST["codDocumento"]))) {
   if (isset($_REQUEST["acao"])) {

            $sSQLs = "SELECT cod_documento, nom_documento FROM sw_documento";
            Sessao::write('sSQLs',$sSQLs);
    }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($_REQUEST["pagina"]);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_documento)","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        if ($dbEmp->numeroDeLinhas == 0 && $_REQUEST["pagina"] != 0) {
            echo 	"<script type='text/javascript'>
                        mudaTelaPrincipal('excluiDocumento.php?".Sessao::getId()."');
                    </script>";
        }
        $dbEmp->vaiPrimeiro();
        $exec = "";
        //$exec .= "<table width='95%'><tr><td class=alt_dados colspan=2>Documento</td></tr>";
        $exec .= "<table width='100%' id='processos'>
                    <tr>
                        <td class=alt_dados colspan=4>
                            Registros de documento
                        </td>
                    </tr>
                    <tr>
                        <td class=labelcenterCabecalho width='5%'>
                            &nbsp;
                        </td>
                        <td class=labelcenterCabecalho width='10%'>
                            Código
                        </td>
                        <td class=labelcenterCabecalho>
                            Descrição
                        </td>
                        <td class=labelcenterCabecalho>
                            &nbsp;
                        </td>
                    </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
            $codDocumentof  = trim($dbEmp->pegaCampo("cod_documento"));
            $nomDocumentof  = trim($dbEmp->pegaCampo("nom_documento"));
            $dbEmp->vaiProximo();
            $nomDocumentof2 = addSlashes($nomDocumentof);
            $exec .= 	"<tr>
                            <td class=show_dados_center_bold>
                                ".$count++."
                            </td>
                            <td class=show_dados_right>
                                ".$codDocumentof."
                            </td>
                            <td class=show_dados>
                                ".$nomDocumentof."
                            </td>
                            <td class=botao width=20 title='Excluir'>
<a href='#'
onClick=\"alertaQuestao('".CAM_PROTOCOLO."protocolo/documento/excluiDocumento.php?".str_replace("&","*_*",Sessao::getId())."*_*codDocumento=".$codDocumentof."*_*stDescQuestao=".addslashes(urlencode($nomDocumentof2))."','sn_excluir','".Sessao::getId()."');\">
                                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0>
                                </a>
                            </td>
                        </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
} else {
include '../documentos.class.php';
$cod = explode(".", $_REQUEST["codDocumento"]);
$codDocumento = $cod[0];
//$nom = pegaDado("nom_documento","sw_documento","Where cod_documento = '".$codDocumento."'");
$documento = new documentos;
$documento->setaVariaveis($codDocumento);
if ($documento->deleteDocumento()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codDocumento);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
                 alertaAviso("'.addslashes(urlencode($_REQUEST["stDescQuestao"])).'","excluir","aviso", "'.Sessao::getId().'");
                 window.location = "excluiDocumento.php?'.Sessao::getId().'&pagina='.$cod[1].'";
                 </script>';
} else {
            echo '<script type="text/javascript">
                 alertaAviso("Documento '.addslashes(urlencode($_REQUEST["stDescQuestao"])).', não pode ser excluído porque está sendo utilizado","n_excluir","erro", "'.Sessao::getId().'");
                 window.location = "excluiDocumento.php?'.Sessao::getId().'&pagina='.$cod[1].'";
                 </script>';
}

}
include '../../../framework/include/rodape.inc.php';
?>

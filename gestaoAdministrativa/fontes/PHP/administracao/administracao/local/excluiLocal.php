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
* Manutneção de locais
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18676 $
$Name$
$Author: cassiano $
$Date: 2006-12-11 14:27:41 -0200 (Seg, 11 Dez 2006) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$stMascaraLocal = pegaConfiguracao("mascara_local");

$excluir = $_REQUEST['excluir'];
$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (isset($excluir)) {
   $controle = 1;
   $excluir = explode("-",$excluir);
   $codOrgao = $excluir[0];
   $codUnidade = $excluir[1];
   $anoE = $excluir[2];
   $codDepartamento = $excluir[3];
   $codSetor = $excluir[4];
   $codLocal = $excluir[5];
   $pagina = $excluir[6];
}

if (!isset($controle)) {
    $controle = 0;
}

if (!isset($pagina)) {
    $pagina = 0;
}

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($controle) {

case 0:
   $sSQLs = "SELECT l.cod_local, l.cod_setor, s.nom_setor, l.cod_departamento, l.nom_local, d.nom_departamento, l.cod_orgao,
   l.cod_unidade, u.nom_unidade, l.ano_exercicio, o.nom_orgao FROM administracao.local as l,administracao.setor as s, administracao.departamento as d, administracao.unidade as u,
   administracao.orgao as o WHERE l.cod_setor = s.cod_setor
            AND l.cod_departamento = s.cod_departamento
            AND l.cod_unidade = s.cod_unidade
            AND l.cod_orgao = s.cod_orgao
            AND l.ano_exercicio = d.ano_exercicio
            AND l.ano_exercicio = o.ano_exercicio
            AND l.ano_exercicio = u.ano_exercicio
            AND l.ano_exercicio = s.ano_exercicio
            AND s.cod_departamento = d.cod_departamento
            AND s.cod_unidade = d.cod_unidade
            AND s.cod_orgao = d.cod_orgao
            AND d.cod_unidade = u.cod_unidade
            AND d.cod_orgao = u.cod_orgao
            AND u.cod_orgao = o.cod_orgao
            and l.cod_local > 0";
        Sessao::write('sSQLs',$sSQLs);
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade), lower(nom_departamento), lower(nom_setor), lower(nom_local)","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if ($dbEmp->numeroDeLinhas==0 && $pagina != 0) {
            mudaTelaPrincipal($PHP_SELF);
        }
        $exec = "";
        $exec .= "
               <table width=100%>
               <tr>
                   <td class='alt_dados' colspan='9'>Registros de locais</td>
               </tr>
               <tr>
                   <td class='labelleft' width='5%'>&nbsp;</td>
                   <td class='labelleft'>Código</td>
                   <td class='labelleft'>Órgão</td>
                   <td class='labelleft'>Unidade</td>
                   <td class='labelleft'>Departamento</td>
                   <td class='labelleft'>Setor</td>
                   <td class='labelleft'>Local</td>
                   <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";
        $count = $paginacao->contador();

        while (!$dbEmp->eof()) {
                $codSetor  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetor  = trim($dbEmp->pegaCampo("nom_setor"));
                $codLocal  = trim($dbEmp->pegaCampo("cod_local"));
                $nomLocal  = trim($dbEmp->pegaCampo("nom_local"));
                $codDepartamento  = trim($dbEmp->pegaCampo("cod_departamento"));
                $nomDepartamento  = trim($dbEmp->pegaCampo("nom_departamento"));
                $codUnidade  = trim($dbEmp->pegaCampo("cod_unidade"));
                $codOrgao  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomUnidade  = trim($dbEmp->pegaCampo("nom_unidade"));
                $anoE  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $nomOrgao  = trim($dbEmp->pegaCampo("nom_orgao"));
                $exclusao = $codOrgao."-".$codUnidade."-".$anoE."-".$codDepartamento."-".$codSetor."-".$codLocal."-".$pagina;
                $dbEmp->vaiProximo();
                $stCodLocal = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor.".".$codLocal."/".$anoE;
                $arCodLocal = validaMascara($stMascaraLocal,$stCodLocal);
                if ($arCodLocal[0]) {
                    $stCodLocal = $arCodLocal[1];
                }
                $exec .= "
                <tr>
                    <td class='labelcenter'>".$count++."</td>
                    <td class=show_dados>".$stCodLocal."</td>
                    <td class=show_dados>".$nomOrgao."</td>
                    <td class=show_dados>".$nomUnidade."</td>
                    <td class=show_dados>".$nomDepartamento."</td>
                    <td class=show_dados>".$nomSetor."</td>
                    <td class=show_dados>".$nomLocal."</td>
                    <td class=botao width=20>
                    <a href='' onClick=\"alertaQuestao('".$_SERVER['PHP_SELF']."?".Sessao::getId()."','excluir','".$exclusao."','".$nomLocal."','sn_excluir','".Sessao::getId()."');\">
                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0></a></td>
                </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
break;

case 1:
   $stCodLocal = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor.".".$codLocal."/".$anoE;
   $arCodLocal = validaMascara($stMascaraLocal,$stCodLocal);
   if ($arCodLocal[0]) {
       $stCodLocal = $arCodLocal[1];
   }
   $nomLocal = pegaDado("nom_local","administracao.local","where cod_orgao='".$codOrgao."' and cod_unidade='".$codUnidade."' and cod_departamento='".$codDepartamento."' and cod_setor='".$codSetor."' and cod_local='".$codLocal."' and ano_exercicio = '".$anoE."'");
   $mensagem = $stCodLocal." - ".$nomLocal;

   $configuracao = new configuracaoLegado;
   $configuracao->setaValorLocal($codLocal,$codSetor,$codDepartamento,$codUnidade,$codOrgao,"",$anoE);
   $pag = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina;
       if ($configuracao->deleteLocal()) {
           $audicao = new auditoriaLegada;
           $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codLocal);
           $audicao->insereAuditoria();
           alertaAviso($pag,$mensagem,"excluir","aviso");
   } else {
       if ( strpos($configuracao->stErro, "fk_") ) {
       $mensagem = "O Local $mensagem não pode ser excluído porque está sendo utilizado";
       }
       alertaAviso($pag,$mensagem,"n_excluir","erro");
   }

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

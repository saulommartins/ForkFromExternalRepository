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
   * Manutneção de relatórios
   * Data de Criação: 25/07/2005

   * @author Analista: Cassiano
   * @author Desenvolvedor: Cassiano

   $Id: relatorioUsuarioMostra.php 65805 2016-06-17 17:32:03Z franver $

   Casos de uso: uc-01.03.94

   */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."paginacaoLegada.class.php";
include CAM_FW_LEGADO."botoesPdfLegado.class.php";

$inCodOrgao = $_REQUEST['hdnUltimoOrgaoSelecionado'];
$orderby    = $_REQUEST['orderby'];

?>

<form action="usuario.php?<?=Sessao::getId()?>" method="POST" name="frm">

<?php

setAjuda("UC-01.03.94");

$comboSet = $_REQUEST['comboSet'];

if (isset($comboSet)) {

   while ( list( $key, $val ) = each( $_POST ) ) {
        $variavel = $key;
        $$variavel = $val;
        $aVarWhere[$key] = $val;

   }

Sessao::write('aWhere', $aVarWhere);

function MontaWhere()
{
   $aVarWhere = Sessao::read('aWhere');

   $i=0;
   while (list($key,$val) = each($aVarWhere)) {
      $variavel = $key;
      $$variavel = $val;

      if ($val<>"XXX" and trim($val)<>"") {
         switch ($key) {
            case "comboSet" :
               $sCampo="set.cod_setor";
               $aCampos[$i] = array($sCampo,"N",$val,"=");
               $i++;
               break;
            case "comboOrg" :
               $sCampo="org.cod_orgao";
               $aCampos[$i] = array($sCampo,"N",$val,"=");
               $i++;
               break;
            case "comboUni" :
               $sCampo="uni.cod_unidade";
               $aCampos[$i] = array($sCampo,"N",$val,"=");
               $i++;
               break;
            case "comboDep" :
               $sCampo="dep.cod_departamento";
               $aCampos[$i] = array($sCampo,"N",$val,"=");
               $i++;
               break;
         }
      }
   }
   $i=0;
   $sWhere = "";
   while ($i<sizeof($aCampos)) {
      if ($aCampos[$i][1]=="T") {
         if ($aCampos[$i][3]=="L") {
            $sParte = $aCampos[$i][0]." like '%".$aCampos[$i][2]."%'";
         } else {
            $sParte = $aCampos[$i][0].$aCampos[$i][3]."'".$aCampos[$i][2]."'";
         }
      } else {
         $sParte = $aCampos[$i][0].$aCampos[$i][3].$aCampos[$i][2];
      }
      if (strlen($sWhere)>0) {
         $sWhere = $sWhere." and ".$sParte;
      } else {
         $sWhere = $sParte;
      }
      $i++;
   }
    if (strlen($sWhere)>0) {
        $sWhere = " AND ".$sWhere;
    }

   return $sWhere;
}

//****************************************************************************

$sWhere = MontaWhere();

$sSQLs =    "SELECT
                  cad.numcgm
                , cad.nom_cgm
                , u.username
                , u.ano_exercicio
                , org.nom_orgao
                , uni.nom_unidade
                , dep.nom_departamento
                , set.nom_setor
                , publico.fn_mascara_dinamica((select valor from administracao.configuracao
                where parametro = 'mascara_setor' and cod_modulo = 2 and exercicio = ".Sessao::getExercicio()."),
                cast(u.cod_orgao
                || '.'|| u.cod_unidade
                || '.' || u.cod_departamento
                || '.' || u.cod_setor
                || '/'|| u.ano_exercicio as varchar)) as codunidsetor
             FROM
                  sw_cgm                as cad
                , administracao.usuario as u
                , administracao.orgao   as org
                , administracao.unidade as uni
                , administracao.departamento as dep
                , administracao.setor as set
             WHERE
                  cad.numcgm = u.numcgm
              AND set.cod_orgao = dep.cod_orgao
              AND set.cod_orgao = uni.cod_orgao
              AND set.cod_orgao = org.cod_orgao
              AND set.cod_unidade = dep.cod_unidade
              AND set.cod_unidade = uni.cod_unidade
              AND set.cod_departamento = dep.cod_departamento
              AND dep.cod_orgao = uni.cod_orgao
              AND dep.cod_unidade = uni.cod_unidade
              AND uni.cod_orgao = org.cod_orgao
              AND u.cod_orgao = org.cod_orgao
              AND u.cod_unidade = uni.cod_unidade
              AND u.cod_departamento = dep.cod_departamento
              AND u.cod_setor = set.cod_setor
              AND u.ano_exercicio = org.ano_exercicio
              AND u.ano_exercicio = uni.ano_exercicio
              AND u.ano_exercicio = dep.ano_exercicio
              AND u.ano_exercicio = set.ano_exercicio";

$sSQLs.=   " AND cad.numcgm <> 0".$sWhere;

Sessao::write('sSQLs',$sSQLs);
Sessao::write('orderby',$orderby);

}

?>

<script type="text/javascript">
    document.frm.action = "usuario.php?<?=Sessao::getId()?>&inCodOrgao=<?=$inCodOrgao?>&orderby=<?=$orderby?>";
    document.frm.submit();
</script>

<?php

   include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

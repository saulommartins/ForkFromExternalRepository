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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desencolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: OCPlanoConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioPlanoConta.class.php"  );

$obRRelatorio                        = new RRelatorio;
$obRContabilidadeRelatorioPlanoConta = new RContabilidadeRelatorioPlanoConta;

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');

if ($arFiltro['inCodGrupo'] != "") {
    $stFiltro .= " substr(cod_estrutural,1,1)::integer IN  (";
    foreach ($arFiltro['inCodGrupo'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 ) . ") AND ";
} else {
    $stFiltro .= $arFiltro['stTodosGrupos'];
}

// seta elementos do filtro para sistema
if ($arFiltro['inCodSistema'] != "") {
    $stFiltro .= " pc.cod_sistema IN  (5,4,"; // <-- Sempre demonstrar nível sintético no relatório
    foreach ($arFiltro['inCodSistema'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 ) . ") AND ";
} else {
    $stFiltro .= $arFiltro['stTodosSistema'];
}

$stEntidades = "";
foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
    $stEntidades .= $valor." , ";
}
$stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 2 );

$obRContabilidadeRelatorioPlanoConta->setCodEstruturalInicial( $arFiltro['stCodEstruturalInicial'] );
$obRContabilidadeRelatorioPlanoConta->setCodEstruturalFinal  ( $arFiltro['stCodEstruturalFinal'] );
$obRContabilidadeRelatorioPlanoConta->obRContabilidadePlanoContaAnalitica->setCodPlanoInicial( $arFiltro['inCodPlanoInicial'] );
$obRContabilidadeRelatorioPlanoConta->obRContabilidadePlanoContaAnalitica->setCodPlanoFinal( $arFiltro['inCodPlanoFinal'] );
$obRContabilidadeRelatorioPlanoConta->setEntidades( $stEntidades );

$obRContabilidadeRelatorioPlanoConta->setFiltro( $stFiltro );
$obRContabilidadeRelatorioPlanoConta->obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
$obRContabilidadeRelatorioPlanoConta->geraRecordSet( $rsPlanoConta );

//sessao->transf5 = $rsPlanoConta;
Sessao::write('rsPlanoConta', $rsPlanoConta);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioPlanoConta.php" );
?>

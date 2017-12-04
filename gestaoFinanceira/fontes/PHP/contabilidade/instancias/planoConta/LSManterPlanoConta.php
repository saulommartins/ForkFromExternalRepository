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

    * Página de Listagem de Itens
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: LSManterPlanoConta.php 64903 2016-04-12 19:44:50Z michel $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterPlanoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";

$stCaminho   = CAM_GF_CONT_INSTANCIAS."planoConta/";

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'alterar');

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgCons; break;
    default         : $pgProx = $pgForm;
}

$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando')) {
    foreach ($request->getAll() as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }

    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $request->get('pg') ? $request->get('pg') : 0);
    Sessao::write('pos', $request->get('pos') ? $request->get('pos') : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $request->get('pg'));
    Sessao::write('pos', $request->get('pos'));
    $request->set('inCodReduzido', $arFiltro['inCodReduzido']);
    $request->set('stCodClass'   , $arFiltro['stCodClass'   ]);
    $request->set('stDescricao'  , $arFiltro['stDescricao'  ]);
    $request->set('inCodEntidade', $arFiltro['inCodEntidade']);
}

if ($request->get('inCodEntidade')) {
   foreach ($request->get('inCodEntidade') as $value) {
       $stCodEntidade .= $value . " , ";
   }
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obRContabilidadePlanoBanco->setExercicio            ( Sessao::getExercicio()                );
$obRContabilidadePlanoBanco->setCodPlano             ( $request->get('inCodReduzido')        );
$obRContabilidadePlanoBanco->setCodEstrutural        ( $request->get('stCodClass')           );
$obRContabilidadePlanoBanco->setNomConta             ( $request->get('stDescricao')          );
$obRContabilidadePlanoBanco->setCodigoEntidade       ( $stCodEntidade                        );
$obRContabilidadePlanoBanco->setNumAgencia           ( $request->get('inNumAgencia')         );
$obRContabilidadePlanoBanco->setNumBanco             ( $request->get('inNumBanco')           );
$obRContabilidadePlanoBanco->setContaCorrente        ( $request->get('stContaCorrente')      );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($request->get('inCodRecurso'));
$obRContabilidadePlanoBanco->listarPlanoContaEntidade( $rsLista, 'cod_estrutural' );

$stLink .= "&stAcao=".$stAcao;
if ($request->get('pg') and  $request->get('pos')) {
    $stLink.= "&pg=".$request->get('pg')."&pos=".$request->get('pos');
}

$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$rsLista->setPrimeiroElemento();
while ( !$rsLista->eof() ) {
    $rsLista->setCampo('cod_estrutural', SistemaLegado::doMask($rsLista->getCampo('cod_estrutural'), $stMascara));
    $rsLista->proximo();
}

$rsLista->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setAjuda('UC-02.02.02');
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Classificação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Reduzido");
$obLista->ultimoCabecalho->setWidth( 9 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Recurso");
$obLista->ultimoCabecalho->setWidth( 9 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_recurso" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "inCodPlano" , "cod_plano" );
$obLista->ultimaAcao->addCampo( "inCodBanco" , "cod_banco" );
$obLista->ultimaAcao->addCampo( "stNumBanco" , "num_banco" );
$obLista->ultimaAcao->addCampo( "inNumBanco" , "num_banco" );
$obLista->ultimaAcao->addCampo( "inCodAgencia" , "cod_agencia" );
$obLista->ultimaAcao->addCampo( "stCodAgencia" , "cod_agencia" );
$obLista->ultimaAcao->addCampo( "stNumAgencia" , "num_agencia" );
$obLista->ultimaAcao->addCampo( "inNumAgencia" , "num_agencia" );
$obLista->ultimaAcao->addCampo( "stExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "inCodConta" , "cod_conta" );
$obLista->ultimaAcao->addCampo( "stNomConta" , "nom_conta" );
$obLista->ultimaAcao->addCampo( "stCodEstrutural", "cod_estrutural" );
$obLista->ultimaAcao->addCampo( "stContaCorrente", "conta_corrente" );
$obLista->ultimaAcao->addCampo( "inContaCorrente", "cod_conta_corrente" );
$obLista->ultimaAcao->addCampo( "inCodRecurso", "cod_recurso" );
$obLista->ultimaAcao->addCampo( "inTipoContaCorrenteTCEPE", "atributo_tcepe" );
$obLista->ultimaAcao->addCampo( "inTipoContaCorrenteTCEMG", "atributo_tcemg" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&stDescQuestao", "nom_conta");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink."&frameDestino=oculto&" );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink."&dtSaldo=".$request->get('dtSaldo')."&" );
}

$obLista->commitAcao();
$obLista->show();
?>

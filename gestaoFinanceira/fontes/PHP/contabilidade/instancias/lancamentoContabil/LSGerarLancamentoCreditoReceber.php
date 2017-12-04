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
    * Página de Listagem de Itens
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: LSGerarLancamentoCreditoReceber.php 60446 2014-10-22 11:46:53Z carlos.silva $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "GerarLancamentoCreditoReceber";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";


$obTOrcamentoReceita = new TOrcamentoReceita;
$obTOrcamentoReceita->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTOrcamentoReceita->recuperaLancamentosCreditosReceber( $rsLista );

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$flTotalizador=0;
$rsLista->setPrimeiroElemento();

while ( !$rsLista->eof() ) {
    $flTotalizador = $flTotalizador + $rsLista->getCampo('vl_original');
    
    $rsLista->setCampo('cod_estrutural', SistemaLegado::doMask($rsLista->getCampo('cod_estrutural'), $stMascara));
    $rsLista->proximo();
}

$rsLista->addFormatacao('vl_original','NUMERIC_BR');
$rsLista->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setAjuda('UC-02.02.02');
$obLista->setMostraPaginacao(false);
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Classificação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Estrutural");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Crédito Tributário");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Original");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_receita] - [descricao]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_estrutural_plano] - [nom_conta]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_original" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->setTotaliza('vl_original,Total:,,5');
$obLista->show();


// Formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "inCodEntidade" );
$obHdnEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.04');
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnEntidade );

// Conclusão do formulário
$obBtnOK = new Ok();
$obBtnLimpar = new Limpar();

$obFormulario->defineBarra(array($obBtnOK, $obBtnLimpar), 'left', '');
$obFormulario->show();

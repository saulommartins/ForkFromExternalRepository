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
    * Página de Formulário para anular contratos
    * Data de Criação   : 07/10/2008

    * @author Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id: FMAnularContrato.php 66522 2016-09-12 18:30:11Z michel $

    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoContrato.class.php";
include_once TCOM."TComprasObjeto.class.php";
include_once TCOM."TComprasFornecedor.class.php";
include_once TLIC."TLicitacaoDocumentosAtributos.class.php";
include_once CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php";

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');
$inNumContrato = $request->get('inNumContrato');
$inCodEntidade = $request->get('inCodEntidade');
$stExercicio   = $request->get('stExercicio');

$obTLicitacaoContrato = new TLicitacaoContrato;
$obTLicitacaoContrato->setDado('num_contrato', $inNumContrato);
$obTLicitacaoContrato->setDado('cod_entidade', $inCodEntidade);
$obTLicitacaoContrato->setDado('exercicio'   , $stExercicio);
$obTLicitacaoContrato->recuperaContratoCompraDireta($rsContrato);

$inCodCompraDireta = $rsContrato->getCampo('cod_compra_direta');
$inCGM = $rsContrato->getCampo('cgm_responsavel_juridico');
$stDataAssinatura = $rsContrato->getCampo('dt_assinatura');
$nmValor = number_format($rsContrato->getCampo('valor_contratado'),2,',','.');
$inExercicioContrato  = $rsContrato->getCampo('exercicio');
$inExercicioCompra  = $rsContrato->getCampo('exercicio_compra_direta');

$obLblExercicioContrato = new Label;
$obLblExercicioContrato->setRotulo ( "Exercício do Contrato");
$obLblExercicioContrato->setValue ( $inExercicioContrato );

$obLblExercicioCompra = new Label;
$obLblExercicioCompra->setRotulo ( "Exercício da Compra");
$obLblExercicioCompra->setValue ( $inExercicioCompra );

$obTCGM = new TCGM;
$obTCGM->setDado('numcgm', $inCGM);
$obTCGM->recuperaPorChave($rsCGM);
$stNomFornecedor = $rsCGM->getCampo('nom_cgm');

$stAcao = $stAcao ? $stAcao : 'incluir';

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnDataAssinatura = new Hidden;
$obHdnDataAssinatura->setName ( "dtAssinatura" );
$obHdnDataAssinatura->setValue( $stDataAssinatura );

$obHdnContrato = new Hidden;
$obHdnContrato->setName( "inNumContrato" );
$obHdnContrato->setValue( $inNumContrato );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "inCodEntidade");
$obHdnEntidade->setValue( $inCodEntidade );

$obLblNumeroContrato = new Label;
$obLblNumeroContrato->setRotulo('Número Contrato');
$obLblNumeroContrato->setValue($inNumContrato);

$obLblNumeroLicitacao= new Label;
$obLblNumeroLicitacao->setRotulo              ( "Número da Compra Direta" );
$obLblNumeroLicitacao->setValue               ( $inCodCompraDireta );

$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo              ( "Fornecedor" );
$obLblFornecedor->setValue               ( $stNomFornecedor );

$obLblDataAssinatura = new Label;
$obLblDataAssinatura->setRotulo('Data da Assinatura');
$obLblDataAssinatura->setValue ( $stDataAssinatura );

$obLblValor = new Label;
$obLblValor->setRotulo('Valor');
$obLblValor->setValue($nmValor);

$obTxtDataAnulacao = new Data;
$obTxtDataAnulacao->setRotulo('Data da Anulação');
$obTxtDataAnulacao->setTitle('Informe a data da anulação.');
$obTxtDataAnulacao->setName('stDataAnulacao');
$obTxtDataAnulacao->setNull(false);

$obTxtValorAnulacao = new Moeda;
$obTxtValorAnulacao->setRotulo('Valor da Anulação');
$obTxtValorAnulacao->setTitle('Informe o valor da anulação.');
$obTxtValorAnulacao->setName('vlAnulacao');
$obTxtValorAnulacao->setNull(false);

$obTxtMotivo = new TextArea;
$obTxtMotivo->setRotulo('Motivo');
$obTxtMotivo->setTitle('Informe o motivo da anulação.');
$obTxtMotivo->setName('stMotivo');
$obTxtMotivo->setNull(false);

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->setAjuda         ("UC-03.05.22");
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnContrato          );
$obFormulario->addHidden        ( $obHdnEntidade         );
$obFormulario->addHidden        ( $obHdnDataAssinatura  );
$obFormulario->addTitulo        ( "Dados do Contrato"   );
$obFormulario->addComponente    ( $obLblExercicioContrato );
$obFormulario->addComponente    ( $obLblExercicioCompra );
$obFormulario->addComponente    ( $obLblNumeroContrato ) ;
$obFormulario->addComponente    ( $obLblNumeroLicitacao );
$obFormulario->addComponente    ( $obLblFornecedor );
$obFormulario->addComponente    ( $obLblDataAssinatura );
$obFormulario->addComponente    ( $obLblValor );
$obFormulario->addComponente    ( $obTxtValorAnulacao );
$obFormulario->addComponente    ( $obTxtDataAnulacao );
$obFormulario->addComponente    ( $obTxtMotivo );

foreach ($request->getAll() as $chave =>$valor) {
    $param.= "&".$chave."=".$valor;
}

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$param;
$obFormulario->Cancelar( $stLocation );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

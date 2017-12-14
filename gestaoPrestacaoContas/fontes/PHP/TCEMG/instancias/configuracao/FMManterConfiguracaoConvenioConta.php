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

    * Página de Formulário para configuração
    * Data de Criação   : 25/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: FMManterConfiguracaoConvenioConta.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * $Revision: 59612 $
    * $Author: gelson $
    * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConvenioPlanoBanco.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoConvenioConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$obRegra = new RContabilidadeLancamentoValor;

//$obTCEMGContaBancaria = new TTCEMGContaBancaria;

$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio      ( Sessao::getExercicio() );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultar( $rs );

$stNomEntidade = $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->getNomCGM();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

// Define Lista de Contas.
$obTCEMGContaConvenio = new TTCEMGConvenioPlanoBanco;
$obTCEMGContaConvenio->setDado    ('exercicio', Sessao::getExercicio() );
$obTCEMGContaConvenio->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obTCEMGContaConvenio->recuperaPlanoContaAnalitica( $rsContas ) ;

$count=0;
for ($i=0; $i<count($rsContas->arElementos);$i++) {
   if ($rsContas->arElementos[$i]['plano_banco']!='NOK') {
      $rsContas->arElementos[$count] = $rsContas->arElementos[$i];
      $count++;
   }
}
$rsContas->inNumLinhas=$count;
$totalOrig=count($rsContas->arElementos);
for ($count2=$count; $totalOrig>$count2;$totalOrig--) {
   unset($rsContas->arElementos[$totalOrig-1]);
}

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Conta Contábil" );

$obLista->setRecordSet( $rsContas );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código Estrutural" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Reduzido" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Conta" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Convênio" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Assinatura" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obTxtConvenio = new TextBox();
$obTxtConvenio->setRotulo('Convênio');
$obTxtConvenio->setName('inNumConvenio_');
$obTxtConvenio->setId  ('inNumConvenio');
$obTxtConvenio->setSize      (30);
$obTxtConvenio->setValue("num_convenio");
$obTxtConvenio->setMaxLength (30);

$obLista->addDadoComponente( $obTxtConvenio );
$obLista->ultimoDado->setCampo( "" );
$obLista->commitDadoComponente();

$obDtAssinatura = new Data();
$obDtAssinatura->setRotulo('Data Assinatura_');
$obDtAssinatura->setName('dtAssinatura_');
$obDtAssinatura->setId  ('dtAssinatura');
$obDtAssinatura->setValue("dt_assinatura");
$obDtAssinatura->setSize      (10);
//$obDtAssinatura->setMaxLength (20);

$obLista->addDadoComponente( $obDtAssinatura );
$obLista->ultimoDado->setCampo( "" );
$obLista->commitDadoComponente();

$obHdnCodPlano = new Hidden;
$obHdnCodPlano->setName ( "inCodPlano_" );
$obHdnCodPlano->setValue( "cod_plano"  );

$obLista->addDadoComponente( $obHdnCodPlano );
$obLista->commitDadoComponente();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_POST['inCodEntidade']  );

//Define o objeto Label Entidade
$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo( "Entidade" );
$obLblCodEntidade->setValue( $_POST['inCodEntidade']." - $stNomEntidade" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnCodEntidade       );

$obFormulario->addTitulo( "Registros de saldos iniciais" );
$obFormulario->addComponente( $obLblCodEntidade   );

$obFormulario->addLista     ( $obLista            );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->Cancelar($stLocation);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

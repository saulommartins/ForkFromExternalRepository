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
    * Formulário de recibo Receita extra
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: $

    * Casos de uso: uc-02.04.29
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include CLA_IAPPLETTERMINAL;
include CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

SistemaLegado::LiberaFrames();

//paginando
$inPg = Sessao::read("pg");
$inPos = Sessao::read("pos");
$boPaginando = Sessao::read("paginando");

//Define o nome dos arquivos PHP
$stPrograma = "ReciboDespesaExtra";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgCons       = "FMConsultar".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

$obTReciboExtra = new TTesourariaReciboExtra;
$obTReciboExtra->setDado('stExercicio'  ,$_GET['stExercicio']  );
$obTReciboExtra->setDado('inCodEntidade',$_GET['inCodEntidade']);
$obTReciboExtra->setDado('tipo_recibo'  ,$_GET['stTipoRecibo'] );
$obTReciboExtra->setDado('inCodRecibo'  ,$_GET['inCodRecibo']  );

//Recupera os dados do recibo extra
$obTReciboExtra->recuperaReciboExtraConsulta($rsReciboExtra);
$rsReciboExtra->addFormatacao('valor','NUMERIC_BR');

//Recupera os dados dos pagamentos do recibo extra
$obTReciboExtra->recuperaReciboDespesaExtraConsultaPagamento($rsReciboExtraPagamento);
$rsReciboExtraPagamento->addFormatacao('valor','NUMERIC_BR');

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//$obIApplet = new IAppletTerminal( $obForm );

//Instancia o label para a entidade
$obLblEntidade = new Label();
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue ($rsReciboExtra->getCampo('cod_entidade').' - '.$rsReciboExtra->getCampo('nom_entidade'));

//Instancia o label para a data de emissao
$obLblDataEmissao = new Label();
$obLblDataEmissao->setRotulo('Data de Emissão');
$obLblDataEmissao->setValue ($rsReciboExtra->getCampo('dt_recibo'));

//Instancia o label para o credor
$obLblCredor = new Label();
$obLblCredor->setRotulo('Credor');
$obLblCredor->setValue ($rsReciboExtra->getCampo('cod_credor').' - '.$rsReciboExtra->getCampo('nom_credor'));

//Instancia o label para recurso
$obLblRecurso = new Label();
$obLblRecurso->setRotulo('Recurso');
$obLblRecurso->setValue ($rsReciboExtra->getCampo('cod_recurso').' - '.$rsReciboExtra->getCampo('nom_recurso'));

//Instancia o label para a conta caixa
$obLblContaCaixa = new Label();
$obLblContaCaixa->setRotulo('Conta Caixa/Banco');
$obLblContaCaixa->setValue ($rsReciboExtra->getCampo('cod_plano_banco').' - '.$rsReciboExtra->getCampo('nom_plano_banco'));

//Instancia o label para a conta receita
$obLblContaReceita = new Label();
$obLblContaReceita->setRotulo('Conta Despesa');
$obLblContaReceita->setValue ($rsReciboExtra->getCampo('cod_plano_receita').' - '.$rsReciboExtra->getCampo('nom_plano_receita'));

//Instancia o label para o valor
$obLblValor = new Label();
$obLblValor->setRotulo('Valor');
$obLblValor->setValue ($rsReciboExtra->getCampo('valor'));

//Instancia o label para o historico
$obLblHistorico = new Label();
$obLblHistorico->setRotulo('Histórico');
$obLblHistorico->setValue ($rsReciboExtra->getCampo('historico'));

//Instancia o label para o status
$obLblStatus = new Label();
$obLblStatus->setRotulo('Status');
$obLblStatus->setValue ($rsReciboExtra->getCampo('status'));

//Instancia uma TableTree para demonstrar os pagamentos
$obTableTree = new TableTree         ();
$obTableTree->setRecordset            ($rsReciboExtraPagamento);
$obTableTree->setArquivo              ($pgOcul);
$obTableTree->setParametros           (array('cod_lote' => 'cod_lote', 'tipo' => 'tipo', 'cod_entidade' => 'cod_entidade', 'exercicio' => 'exercicio'));
$obTableTree->setComplementoParametros('stCtrl=montaEstornos');
$obTableTree->setSummary              ('Pagamentos');
$obTableTree->addCondicionalTree      ( 'estornado' , 't' );
//$obTableTree->setConditional          (true , '#efefef');
$obTableTree->setSummary              ('Lista de Pagamentos');
$obTableTree->Head->addCabecalho      ('Data',10);
$obTableTree->Head->addCabecalho      ('Conta Caixa/Banco',60);
$obTableTree->Head->addCabecalho      ('Valor',10);
$obTableTree->Body->addCampo          ('dt_pagamento','C');
$obTableTree->Body->addCampo          ('[cod_plano] - [nom_plano]', 'E');
$obTableTree->Body->addCampo          ('valor','D');
$obTableTree->montaHTML               ();

//Instancia um span para os pagamentos
$obSpnPagamentos = new Span();
$obSpnPagamentos->setId   ('spnPagamentos');
$obSpnPagamentos->setValue($obTableTree->getHTML());

//cria um button para a acao voltar
$obBtnVoltar = new Button;
$obBtnVoltar->setName              ( "btnVoltar" );
$obBtnVoltar->setValue             ( "Voltar" );
$obBtnVoltar->setTipo              ( "button" );
$obBtnVoltar->obEvento->setOnClick ( "document.location = '".$pgList."?".Sessao::getId()."&stAcao=consultar&pg=".$inPg."&pos=".$inPos."';" );
$obBtnVoltar->setDisabled          ( false );

//Monta o formulario
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm                 );
$obFormulario->addHidden    ($obHdnAcao              );
$obFormulario->addHidden    ($obHdnCtrl              );
//$obFormulario->addHidden    ($obIApplet              );

$obFormulario->addComponente($obLblEntidade         );
$obFormulario->addComponente($obLblDataEmissao      );
$obFormulario->addComponente($obLblCredor           );
$obFormulario->addComponente($obLblRecurso          );
$obFormulario->addComponente($obLblContaCaixa       );
$obFormulario->addComponente($obLblContaReceita     );
$obFormulario->addComponente($obLblValor             );
$obFormulario->addComponente($obLblHistorico        );
$obFormulario->addComponente($obLblStatus           );
$obFormulario->addSpan      ($obSpnPagamentos       );

$obFormulario->defineBarra( array($obBtnVoltar), 'left', '' );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

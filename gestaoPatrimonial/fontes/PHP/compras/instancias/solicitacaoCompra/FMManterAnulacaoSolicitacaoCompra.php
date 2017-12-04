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

   /*
    * Arquivo com o formulário de anulação de solicitação
    * Data de Criação   : 04/09/2009

    * @author Analista      Gelson Gonçalves
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ILabelEntidade.class.php";
include_once CAM_GP_ALM_COMPONENTES."ILabelAlmoxarifado.class.php";
include_once CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/componentes/ILabelCGM.class.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoEntrega.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoHomologada.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoConvenio.class.php";

$stPrograma = "ManterAnulacaoSolicitacaoCompra";

$pgFilt = "FLManterSolicitacaoCompra.php";
$pgList = "LSManterSolicitacaoCompra.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PRManterSolicitacaoCompra.php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::write('arValores' , array());

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'];

$obTComprasSolicitacao           = new TComprasSolicitacao;
$obTComprasSolicitacaoEntrega    = new TComprasSolicitacaoEntrega;
$obTComprasSolicitacaoHomologada = new TComprasSolicitacaoHomologada;
$obTComprasSolicitacaoConvenio   = new TComprasSolicitacaoConvenio;
$rsHomologada                    = new RecordSet;

$obTComprasSolicitacao->setDado('cod_solicitacao' , $_GET['cod_solicitacao']);
$obTComprasSolicitacao->setDado('exercicio'       , $_GET['exercicio']);
$obTComprasSolicitacao->setDado('cod_entidade'    , $_GET['cod_entidade']);
$obTComprasSolicitacao->consultar();

$stFiltro .= " AND NOT EXISTS
                   (
                        SELECT  1
                          FROM  compras.solicitacao_homologada_anulacao
                         WHERE  solicitacao_homologada_anulacao.exercicio       = solicitacao_homologada.exercicio
                           AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao_homologada.cod_entidade
                           AND  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                   )";

$obTComprasSolicitacaoHomologada->setDado('cod_solicitacao' , $_GET['cod_solicitacao']);
$obTComprasSolicitacaoHomologada->setDado('exercicio'       , Sessao::getExercicio());
$obTComprasSolicitacaoHomologada->setDado('cod_entidade'    , $_GET['cod_entidade']);

$obTComprasSolicitacaoHomologada->recuperaboHomologada( $rsHomologada, $stFiltro );

$boHomologada = ($rsHomologada->EOF()) ? "Não" : "Sim";

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "stExercicio"      );
$obHdnExercicio->setValue ( $_GET['exercicio'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "stCodEntidade" );
$obHdnCodEntidade->setValue ( $_GET['cod_entidade'] );

$obHdnCodSolicitacao = new Hidden;
$obHdnCodSolicitacao->setName  ( "stCodSolicitacao" );
$obHdnCodSolicitacao->setValue ( $_GET['cod_solicitacao'] );

include_once $pgOcul;

$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

////Exercicio
$obLblExercicio = new Label;
$obLblExercicio->setId     ( 'stExercicio' );
$obLblExercicio->setrotulo ( 'Exercício'   );
$obLblExercicio->setValue  ( $obTComprasSolicitacao->getDado('exercicio')   );

///Entidade
$obILabelEntidade = new ILabelEntidade( $obForm );
$obILabelEntidade->setMostraCodigo( true           );
$obILabelEntidade->setCodEntidade ( $obTComprasSolicitacao->getDado( 'cod_entidade' )   );

/// Solicitação
$obLblSolicitacao = new Label;
$obLblSolicitacao->setId     ( 'stSolicitacao' );
$obLblSolicitacao->setrotulo ( 'Solicitação'   );
$obLblSolicitacao->setValue  ( $_GET['cod_solicitacao']  );

/// almoxarifado
$obLblAlmoxarifado = new ILabelAlmoxarifado($obForm);
$obLblAlmoxarifado->setCodAlmoxarifado( $obTComprasSolicitacao->getDado( 'cod_almoxarifado' ) );

//Formatar data buscada no banco (timestamp)
$data = $obTComprasSolicitacao->getDado( 'timestamp' );
$ano = substr($data, 0,4); // mes
$mes = substr($data, 5, 2); // dia
$dia = substr($data, 8, 2); // ano
$dataFormatada = $dia.'/'.$mes.'/'.$ano;

/// Data da solicitação
$obLblDataSolicitacao = new Label;
$obLblDataSolicitacao->setId     ( 'stDataSolicitacao' );
$obLblDataSolicitacao->setrotulo ( 'Data Solicitação' );
$obLblDataSolicitacao->setValue  ( $dataFormatada );

/// Objeto de compras
$obLblObjeto = new ILabelEditObjeto();
$obLblObjeto->setRotulo    ( "Objeto" );
$obLblObjeto->setCodObjeto ( $obTComprasSolicitacao->getDado('cod_objeto'));

///Requisitante CGM
$obLblRequisitante = new ILabelCGM();
$obLblRequisitante->setRotulo ( "Requisitante" );
$obLblRequisitante->setNumCGM ( $obTComprasSolicitacao->getDado('cgm_requisitante'));

/// Solicitante CGM
$obLblSolicitante = new ILabelCGM();
$obLblSolicitante->setRotulo ( "Solicitante" );
$obLblSolicitante->setNumCGM ( $obTComprasSolicitacao->getDado('cgm_solicitante'));

//Motivo da anulação da solicitação
$obTxtMotivo = new TextArea;
$obTxtMotivo->setName   ( "stMotivo"         );
$obTxtMotivo->setId     ( "stMotivo"         );
$obTxtMotivo->setValue  ( $stObservacao      );
$obTxtMotivo->setRotulo ( "Motivo"           );
$obTxtMotivo->setTitle  ( "Informe o motivo" );
$obTxtMotivo->setNull   ( false              );
$obTxtMotivo->setRows   ( 2                  );
$obTxtMotivo->setCols   ( 100                );
$obTxtMotivo->setMaxCaracteres( 255          );

/// Localização/Entrega CGM
$obTComprasSolicitacaoEntrega->setDado('cod_solicitacao', $_GET['cod_solicitacao']);
$obTComprasSolicitacaoEntrega->setDado('exercicio'      , Sessao::getExercicio());
$obTComprasSolicitacaoEntrega->setDado('cod_entidade'   , $_GET['cod_entidade']);
$obTComprasSolicitacaoEntrega->consultar();

$obLblEntrega = new ILabelCGM();
$obLblEntrega->setRotulo ( "Localização de Entrega" );
$obLblEntrega->setNumCGM ( $obTComprasSolicitacaoEntrega->getDado( 'numcgm' ) );

////Prazo
$obLblPrazoEntrega = new Label;
$obLblPrazoEntrega->setrotulo ( 'Prazo de Entrega'   );
$obLblPrazoEntrega->setValue  ( $obTComprasSolicitacao->getDado('prazo_entrega').' dias' );

////Observação
$obLblObservacao = new Label;
$obLblObservacao->setrotulo ( 'OBS/Justificativa'   );
$obLblObservacao->setValue  ( stripslashes($obTComprasSolicitacao->getDado('observacao')) );

///Convênio
$obTComprasSolicitacaoConvenio->setDado('cod_solicitacao' , $_GET['cod_solicitacao']);
$obTComprasSolicitacaoConvenio->setDado('exercicio'       , Sessao::getExercicio());
$obTComprasSolicitacaoConvenio->setDado('cod_entidade'    , $_GET['cod_entidade']);
$obTComprasSolicitacaoConvenio->consultar();

# Homologação
$obLblHomologacao = new Label;
$obLblHomologacao->setrotulo ( 'Homologada'  );
$obLblHomologacao->setValue  ( $boHomologada );

$obSpnItens = new Span;
$obSpnItens->setId ( 'spnListaItens' );

$obBtnCancelar = new Button;
$obBtnCancelar->setName                    ( "btnClean"                        );
$obBtnCancelar->setValue                   ( "Cancelar"                        );
$obBtnCancelar->setTipo                    ( "button"                          );
$obBtnCancelar->setDisabled                ( false                             );
$obBtnCancelar->obEvento->setOnClick       ( "Cancelar('".$stLocation."');"    );

$obBtnOK = new Ok(true);

$botoesForm = array ( $obBtnOK , $obBtnCancelar );

$obFormulario = new Formulario;

$obFormulario->addForm       ( $obForm                );
$obFormulario->addHidden     ( $obHdnAcao             );
$obFormulario->addHidden     ( $obHdnCtrl             );
$obFormulario->addHidden     ( $obHdnExercicio        );
$obFormulario->addHidden     ( $obHdnCodEntidade      );
$obFormulario->addHidden     ( $obHdnCodSolicitacao   );
$obFormulario->addTitulo     ( 'Dados da Solicitação' );
$obFormulario->addComponente ( $obLblExercicio        );
$obFormulario->addComponente ( $obLblDataSolicitacao  );
$obFormulario->addComponente ( $obILabelEntidade      );
$obFormulario->addComponente ( $obLblSolicitacao      );
$obFormulario->addComponente ( $obLblAlmoxarifado     );
$obFormulario->addComponente ( $obLblObjeto           );
$obFormulario->addComponente ( $obLblRequisitante     );
$obFormulario->addComponente ( $obLblSolicitante      );
$obFormulario->addComponente ( $obTxtMotivo           );
$obFormulario->addSpan       ( $obSpnItens            );
$obFormulario->defineBarra   ( $botoesForm            );
$obFormulario->show();

$jsOnLoad = "montaParametrosGET('carregaLista');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

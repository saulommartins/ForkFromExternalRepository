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
    * Pagina de formulário para Cadastro de Comissão de licitação
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    $Id: FLManterComissaoLicitacao.php 61017 2014-11-28 18:14:03Z carlos.silva $

    * Casos de uso: uc-03.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoTipoComissao.class.php'                               );
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoTipoMembro.class.php'                                 );

//Define o nome dos arquivos PHP
$stPrograma = "ManterComissaoLicitacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once ( $pgOcul );

$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal");

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];
$stAtoDesignacao = $_REQUEST['stAtoDesignacao'];
$stAtoDesignacaoMembro = $_REQUEST['stAtoDesignacaoMembro'];
$inCodNorma = $_REQUEST['inCodNorma'];
$dtVigencia = $_REQUEST['dtVigencia'];

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//// numero da comissão
$obTxtNumComissao = new TextBox;
$obTxtNumComissao->setRotulo  ( "Código da Comissão"            );
$obTxtNumComissao->setName    ( "txtCodComissao"                );
$obTxtNumComissao->setTitle   ( "Informe o código da comissão." );
$obTxtNumComissao->setSize    ( 10                              );
$obTxtNumComissao->setInteiro ( true                            );

/// Finalidade da Comissão
$obTTipoComissao = new TLicitacaoTipoComissao;
$obTTipoComissao->recuperaTodos($rsTiposComissao);

$obCmbFinalidade = new Select;
$obCmbFinalidade->setRotulo             ( 'Finalidade da Comissão'                              );
$obCmbFinalidade->setTitle              ( 'Selecione a finalidade da comissão a ser cadastrada.');
$obCmbFinalidade->setName               ( "stFinalidade"                                        );
$obCmbFinalidade->setId                 ( "stFinalidade"                                        );
$obCmbFinalidade->setValue              ( $stFinalidade                                         );
$obCmbFinalidade->setStyle              ( "width: 200px"                                        );
$obCmbFinalidade->addOption             ( "", "Selecione"                                       );
while ( !$rsTiposComissao->eof() ) {
    $obCmbFinalidade->addOption( $rsTiposComissao->getCampo( 'cod_tipo_comissao' ) , $rsTiposComissao->getCampo( 'descricao' ) );
    $rsTiposComissao->proximo();
}

///nro do ato de Designação

$obHdnDataDesignacaoComissao = new Hidden;
$obHdnDataDesignacaoComissao->setName ( 'stComData');
$obHdnDataDesignacaoComissao->setId   ( 'stComData');
$obHdnDataDesignacaoComissao->setValue ( 'N' );

$obBscAtoDesignacao = new BuscaInner;
$obBscAtoDesignacao->setRotulo  ( "Número do Ato de Designação"                     );
$obBscAtoDesignacao->setTitle   ( 'Selecione, no Normas, o número do ato jurídico de designação da comissão ( Decreto, Portaria, etc.' );
$obBscAtoDesignacao->setNull    ( true );
$obBscAtoDesignacao->setId      ( "stAtoDesignacao"         );
$obBscAtoDesignacao->setValue   ( $stAtoDesignacao          );
$obBscAtoDesignacao->obCampoCod->setName     ( "inCodNorma" );
$obBscAtoDesignacao->obCampoCod->setId       ( "inCodNorma" );
$obBscAtoDesignacao->obCampoCod->setSize     ( 10           );
$obBscAtoDesignacao->obCampoCod->setMaxLength( 7            );
$obBscAtoDesignacao->obCampoCod->setValue    ( $inCodNorma  );
$obBscAtoDesignacao->obCampoCod->setAlign    ( "left"       );
$obBscAtoDesignacao->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('buscaNorma','inCodNorma,stComData' );" );
$obBscAtoDesignacao->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stAtoDesignacao','','".Sessao::getId()."','800','550');");

////// CGM
$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setTipo ( "fisica" );
$obIPopUpCGM->setTitle( "Informe o CGM relacionado ao Membro da Comissão.");
$obIPopUpCGM->setNull            ( true );

/////Tipo do membro
$obTTipoMembro = new TLicitacaoTipoMembro;
$obTTipoMembro->recuperaTodos( $rsTiposMembro );
$obCmbTipoMembro = new Select;
$obCmbTipoMembro->setRotulo  ( 'Tipo do Membro'                                        );
$obCmbTipoMembro->setTitle   ( 'Selecione o tipo do membro que está sendo cadastrado.' );
$obCmbTipoMembro->setName    ( "stTipoMembro"                                          );
$obCmbTipoMembro->setId      ( "stTipoMembro"                                          );
$obCmbTipoMembro->setValue   ( $stTipoMembro                                           );
$obCmbTipoMembro->setStyle   ( "width: 200px"                                          );
$obCmbTipoMembro->addOption  ( "", "Selecione"                                         );
while ( !$rsTiposMembro->eof() ) {
    $obCmbTipoMembro->addOption ( $rsTiposMembro->getCampo( 'cod_tipo_membro' ) , $rsTiposMembro->getCampo ( 'descricao' ) );
    $rsTiposMembro->proximo();
}

///nro do ato de Designação do Membro
$obBscAtoDesignacaoMembro = new BuscaInner;
$obBscAtoDesignacaoMembro->setRotulo  ( "Número do Ato de Designação do Membro"                     );
$obBscAtoDesignacaoMembro->setTitle   ( 'Selecione, no Normas, o número do ato jurídico de designação da comissão ( Decreto, Portaria, etc.' );
$obBscAtoDesignacaoMembro->setNull    ( false );
$obBscAtoDesignacaoMembro->setId      ( "stAtoDesignacaoMembro"         );
$obBscAtoDesignacaoMembro->setValue   ( $stAtoDesignacaoMembro          );
$obBscAtoDesignacaoMembro->obCampoCod->setName     ( "inCodNormaMembro" );
$obBscAtoDesignacaoMembro->obCampoCod->setId       ( "inCodNormaMembro" );
$obBscAtoDesignacaoMembro->obCampoCod->setSize     ( 10                 );
$obBscAtoDesignacaoMembro->obCampoCod->setMaxLength( 7                  );
$obBscAtoDesignacaoMembro->obCampoCod->setValue    ( $inCodNorma        );
$obBscAtoDesignacaoMembro->obCampoCod->setAlign    ( "left"             );
$obBscAtoDesignacaoMembro->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('buscaNormaMembro','inCodNormaMembro, stComData' );" );
$obBscAtoDesignacaoMembro->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNormaMembro','stAtoDesignacaoMembro','','".Sessao::getId()."','800','550');");
$obBscAtoDesignacaoMembro->setNull             ( true );

//// Aitvas e/ou inativas
$obCmbSituacao = new Select;
$obCmbSituacao->setRotulo ( 'Situação da Comissão' );
$obCmbSituacao->setTitle  ( 'Selecione a situação.');
$obCmbSituacao->setName   ( "stSituacao"           );
$obCmbSituacao->setId     ( "stSituacao"           );
$obCmbSituacao->setValue  ( '1'                    );
$obCmbSituacao->setStyle  ( "width: 200px"         );
$obCmbSituacao->addOption ( "1", "Todas"           );
$obCmbSituacao->addOption ( "2", "Ativas"          );
$obCmbSituacao->addOption ( "3", "Inativas"        );

// dt final vigencia
$obTxtDataVigencia = new Label;
$obTxtDataVigencia->setName                       ( "dtVigencia"                  );
$obTxtDataVigencia->setID                         ( "dtVigencia"                  );
$obTxtDataVigencia->setValue                      ( $dtVigencia                   );
$obTxtDataVigencia->setRotulo                     ( "Vigência"                    );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                      );
$obFormulario->addHidden     ( $obHdnAcao                   );
$obFormulario->addHidden     ( $obHdnCtrl                   );
$obFormulario->addTitulo     ( 'Dados para Filtro'          );
$obFormulario->addComponente ( $obTxtNumComissao            );
$obFormulario->addComponente ( $obCmbFinalidade             );
$obFormulario->addComponente ( $obBscAtoDesignacao          );
$obFormulario->addComponente ( $obTxtDataVigencia           );
$obFormulario->addComponente ( $obIPopUpCGM                 );
$obFormulario->addComponente ( $obCmbTipoMembro             );
$obFormulario->addComponente ( $obBscAtoDesignacaoMembro    );
$obFormulario->addComponente ( $obCmbSituacao               );
$obFormulario->addHidden     ( $obHdnDataDesignacaoComissao );

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

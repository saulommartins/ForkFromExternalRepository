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
    * Filtro de configuração de Obras e Serviços de Engenharia
    * Data de Criação   : 21/09/2015
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: FLManterConfiguracaoObrasServicos.php 63632 2015-09-22 17:42:03Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GP_COM_MAPEAMENTO.'TComprasModalidade.class.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATipoObra.class.php';

$stPrograma = 'ManterConfiguracaoObrasServicos';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obTTCMBATipoObra = new TTCMBATipoObra;
$stOrder = " ORDER BY descricao ";
$obTTCMBATipoObra->recuperaTodos($rsTipoObra, "", $stOrder);

$obTxtExercicio = new TextBox();
$obTxtExercicio->setName        ( 'stExercicio'             );
$obTxtExercicio->setId          ( 'stExercicio'             );
$obTxtExercicio->setRotulo      ( 'Exercício'               );
$obTxtExercicio->setMaxLength   ( 4                         );
$obTxtExercicio->setSize        ( 5                         );
$obTxtExercicio->setNull        ( false                     );
$obTxtExercicio->setInteiro     ( true                      );
$obTxtExercicio->setValue       ( Sessao::getExercicio()    );

$obEntidade = new ITextBoxSelectEntidadeUsuario;
$obEntidade->setCodEntidade($request->get('cod_entidade'));
$obEntidade->setNull( false );
$obEntidade->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");
$obEntidade->obSelect->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");

$obCmbTipoObra = new Select();
$obCmbTipoObra->setName         ( "inCodTipoObra"                   );
$obCmbTipoObra->setRotulo       ( "Tipo Obra"                       );
$obCmbTipoObra->setId           ( "stTipoObra"                      );
$obCmbTipoObra->setCampoId      ( "cod_tipo"                        );
$obCmbTipoObra->setCampoDesc    ( "descricao"                       );
$obCmbTipoObra->addOption       ( '','Selecione'                    );
$obCmbTipoObra->preencheCombo   ( $rsTipoObra                       );
$obCmbTipoObra->setNull         ( true                              );
$obCmbTipoObra->setValue        ( $request->get('inCodTipoObra')    );

$obTxtNroObra = new TextBox;
$obTxtNroObra->setName     ( "stNroObra"                  );
$obTxtNroObra->setId       ( "stNroObra"                  );
$obTxtNroObra->setValue    ( $request->get('stNroObra')   );
$obTxtNroObra->setRotulo   ( "Número da Obra"             );
$obTxtNroObra->setTitle    ( "Informe o número da Obra."  );
$obTxtNroObra->setNull     ( true                         );
$obTxtNroObra->setSize     ( 21                           );
$obTxtNroObra->setMaxLength( 10                           );

//Consulta para Buscar Modalidades Licitação
$obComprasModalidade = new TComprasModalidade();
$stFiltro = "";
$stOrdem  = " ORDER BY cod_modalidade, descricao ";
$obComprasModalidade->recuperaTodos($rsModalidade, $stFiltro, $stOrdem);

//Montando Licitação Urbem
$obTxtExercicioLicitacao = new TextBox();
$obTxtExercicioLicitacao->setName       ( 'stExercicioLicitacao'                                        );
$obTxtExercicioLicitacao->setId         ( 'stExercicioLicitacao'                                        );
$obTxtExercicioLicitacao->setRotulo     ( 'Exercício'                                                   );
$obTxtExercicioLicitacao->setMaxLength  ( 4                                                             );
$obTxtExercicioLicitacao->setSize       ( 5                                                             );
$obTxtExercicioLicitacao->setNull       ( true                                                          );
$obTxtExercicioLicitacao->setValue      ( $request->get('stExercicioLicitacao', Sessao::getExercicio()) );
$obTxtExercicioLicitacao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");

$obISelectModalidade = new Select();
$obISelectModalidade->setName       ( 'inCodModalidade'                         );
$obISelectModalidade->setId         ( 'inCodModalidade'                         );
$obISelectModalidade->setRotulo     ( 'Modalidade'                              );
$obISelectModalidade->setTitle      ( 'Selecione a Modalidade da Licitação.'    );
$obISelectModalidade->setCampoID    ( 'cod_modalidade'                          );
$obISelectModalidade->setValue      ( $request->get('inCodModalidade')          );
$obISelectModalidade->setCampoDesc  ( '[cod_modalidade] - [descricao]'          );
$obISelectModalidade->addOption     ( '','Selecione'                            );
$obISelectModalidade->setNull       ( true                                      );
$obISelectModalidade->preencheCombo ( $rsModalidade                             );
$obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$request->get('inCodLicitacao')."', 'carregaLicitacao');");

$obISelectLicitacao = new Select();
$obISelectLicitacao->setName    ( 'inCodLicitacao'                  );
$obISelectLicitacao->setId      ( 'inCodLicitacao'                  );
$obISelectLicitacao->setRotulo  ( 'Licitação'                       );
$obISelectLicitacao->setTitle   ( 'Selecione a Licitação.'          );
$obISelectLicitacao->addOption  ( '','Selecione'                    );
$obISelectLicitacao->setNull    ( true                              );
$obISelectLicitacao->setValue   ( $request->get('inCodLicitacao')   );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addTitulo("Dados para filtro");
$obFormulario->addComponente($obTxtExercicio);
$obFormulario->addComponente($obEntidade);
$obFormulario->addComponente($obCmbTipoObra);
$obFormulario->addComponente($obTxtNroObra);

$obFormulario->addTitulo("Licitação");
$obFormulario->addComponente($obTxtExercicioLicitacao);
$obFormulario->addComponente($obISelectModalidade);
$obFormulario->addComponente($obISelectLicitacao);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

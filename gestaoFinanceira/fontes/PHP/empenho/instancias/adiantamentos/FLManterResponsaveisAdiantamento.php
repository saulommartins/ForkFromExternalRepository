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
    * Página de Formulário de responsáveis por adiantamento
    * Data de Criação   : 13/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso : uc-02.03.32
*/

/*
$Log$
Revision 1.2  2007/08/27 20:37:58  luciano
Bug#10007#

Revision 1.1  2007/08/10 13:27:08  luciano
movido de lugar

Revision 1.6  2007/07/05 16:12:21  luciano
Bug#9366#,Bug#9368#

Revision 1.5  2007/03/26 20:02:54  luciano
#8819#

Revision 1.4  2007/03/08 19:55:03  luciano
#8614#

Revision 1.3  2007/03/07 15:33:52  luciano
#8610#

Revision 1.2  2007/02/22 17:34:00  luciano
#8359#

Revision 1.1  2006/10/18 18:58:11  rodrigo
Caso de Uso 02.03.32

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//Componentes
include_once ( CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php'                             );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"                                         );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);
Sessao::remove('arValores');

$stPrograma = "ManterResponsaveisAdiantamento";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//Definição do Form
$obForm = new Form;
$obForm->setAction ( $pgList  );
$obForm->setTarget ( "telaPrincipal" );

//Define o objeto de controle
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"  );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

// Campo Responsável
$obBscResponsavel = new IPopUpCGM( $obForm );
$obBscResponsavel->setNull     ( true               );
$obBscResponsavel->setRotulo   ( "Responsável"          );
$obBscResponsavel->setId       ( "campoInner2" );
$obBscResponsavel->setTitle    ( "Informe o CGM do responsável" );
$obBscResponsavel->setValue( $stNomeResponsavel);
$obBscResponsavel->obCampoCod->setName("inCodigoResponsavel");
$obBscResponsavel->obCampoCod->setValue( $inCodigoResponsavel );
$obBscResponsavel->obCampoCod->setSize ( 10 );
$obBscResponsavel->setTipo ( 'fisica' );

//Campo Contrapartida Lançamento
$obPopUpContraPartida = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect                                       );
$obPopUpContraPartida->setID                     ( 'innerContraPartida'                                               );
$obPopUpContraPartida->setName                   ( 'innerContraPartida'                                               );
$obPopUpContraPartida->obCampoCod->setName       ( "inCodigoContraPartida"                                          );
$obPopUpContraPartida->setRotulo                 ( 'Contrapartida Contábil'                                         );
$obPopUpContraPartida->setTitle                  ( 'Informe o código da conta do Passivo Compensado'                );
$obPopUpContraPartida->setTipoBusca              ( 'tes_contrapartida_lancamento'                                      );

//Campo Conta Lançamento
$obPopUpContaPartida = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect                                );
$obPopUpContaPartida->setID                     ( 'innerContaLancamento'                                      );
$obPopUpContaPartida->setName                   ( 'innerContaLancamento'                                      );
$obPopUpContaPartida->obCampoCod->setName       ( "inCodigoContaLancamento"                                      );
$obPopUpContaPartida->setRotulo                 ( 'Conta Contábil'                                          );
$obPopUpContaPartida->setTitle                  ( 'Informe o código da conta do Ativo Compensado ' );
$obPopUpContaPartida->setTipoBusca              ( 'emp_conta_lancamento_adiantamentos'                               );

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                                                      );
$obFormulario->addHidden        ( $obHdnAcao                                                   );
$obFormulario->addHidden        ( $obHdnCtrl                                                   );
$obFormulario->addTitulo        ( "Dados para Filtro de Responsáveis Por Adiantamento"         );
$obFormulario->addComponente    ( $obBscResponsavel                                            );
$obFormulario->addComponente    ( $obPopUpContraPartida                                        );
$obFormulario->addComponente    ( $obPopUpContaPartida                                         );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

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
    * Página de filtro para o cadastro de face de quadra
    * Data de Criação   : 17/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: FLBuscaFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.4  2006/09/15 15:04:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once(CAM_MAPEAMENTO."TTipoLogradouro.class.php");
    include_once(CAM_MAPEAMENTO."TMunicipio.class.php");
    include_once(CAM_MAPEAMENTO."TUF.class.php");
    include_once(CAM_MAPEAMENTO."TConfiguracao.class.php");
    include_once(CAM_MAPEAMENTO."TNivel.class.php");
    include_once(CAM_MAPEAMENTO."TNivelSuperior.class.php");
    include_once(CAM_MAPEAMENTO."TAtributoNivel.class.php");
    include_once(CAM_MAPEAMENTO."TLocalizacao.class.php");
    include_once(CAM_INTERFACE."MontaLocalizacao.class.php");

    //Define o nome dos arquivos PHP
    $stPrograma = "BuscaLote";
    $pgFilt = "FL".$stPrograma.".php";
    $pgList = "LS".$stPrograma.".php";
    $pgForm = "FM".$stPrograma.".php";
    $pgProc = "PR".$stPrograma.".php";
    $pgOcul = "OC".$stPrograma.".php";

    //Definições das funções de formulário
    $stFncJavaScript .= " function buscaValor(stCampo,inQuantNiveis) { \n";
    $stFncJavaScript .= "     document.frm.target = 'oculto'; \n";
    $stFncJavaScript .= "     document.frm.stCtrl.value = 'preencheCombos'; \n";
    $stFncJavaScript .= "     document.frm.action = '".$pgOcul."?".Sessao::getId()."&stSelecionado=' + stCampo + '&inQuantNiveis=' + inQuantNiveis; \n";
    $stFncJavaScript .= "     document.frm.submit(); \n";
    $stFncJavaScript .= "     document.frm.target = ''; \n";
    $stFncJavaScript .= "     document.frm.action = '".$pgList."?".Sessao::getId()."'; \n";
    $stFncJavaScript .= " } \n";

    //Define o objeto TEXT para armenzar a MASCARA DE LOCALIZACAO
    $obTNivelMascara = new TNivel;
    $stMascara = $obTNivelMascara->recuperaMascara();

    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( $pgList );

    //Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName( "stCtrl" );
    $obHdnCtrl->setValue( "" );

    $obHdnCampoNom = new Hidden;
    $obHdnCampoNom->setName( "campoNom" );
    $obHdnCampoNom->setValue( $_GET["campoNom"] );

    $obHdnCampoNum = new Hidden;
    $obHdnCampoNum->setName( "campoNum" );
    $obHdnCampoNum->setValue( $_GET["campoNum"] );

    //Define o objeto para o código do logradouro
    $obIntCodLote = new TextBox;
    $obIntCodLote->setName( "inCodLote" );
    $obIntCodLote->setRotulo( "Número do lote" );
    $obIntCodLote->setSize( 20 );
    $obIntCodLote->setValue( $inCodLote );

    $obMontaLocalizacao = new MontaLocalizacao;
    $obMontaLocalizacao->setName    ('inCodLocalizacao_');
    $obMontaLocalizacao->setRotulo  ('Localizacao');
    $obMontaLocalizacao->setNull    (true);
    $obMontaLocalizacao->setActionAnterior ( $pgOcul );
    $obMontaLocalizacao->setActionPosterior( $pgList );

    //Criação do formulário
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obFormulario->obJavaScript->addFuncao( $stFncJavaScript );

    $obFormulario->addHidden( $obHdnCtrl );
    $obFormulario->addHidden( $obHdnCampoNom );
    $obFormulario->addHidden( $obHdnCampoNum );
    $obFormulario->addTitulo( "Dados para imóvel" );
    $obFormulario->addComponente( $obIntCodLote );
    $obMontaLocalizacao->geraFormulario( $obFormulario );

    $obFormulario->addIFrameOculto("oculto");

    $obFormulario->OK();
    $obFormulario->show();

    include_once '../../../includes/rodape.php';

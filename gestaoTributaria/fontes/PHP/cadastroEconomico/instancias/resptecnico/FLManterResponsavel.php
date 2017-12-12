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
    * Filtro para Economico >> Responsavel Tecnico
    * Data de Criação   : 18/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FLManterResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.9  2007/02/26 17:47:44  cassiano
Bug #8428#

Revision 1.8  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"     );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"                 );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"           );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterResponsavel";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

// INSTANCIA REGRAS UTILIZADAS

$obRConselho            = new RConselho                 ;
$obRResponsavelTecnico  = new RCEMResponsavelTecnico    ;
$obRProfissao           = new RProfissao                ;
$obRUF                  = new RUF                       ;

$obRProfissao->listarProfissao($rsProfissoes);

$obRUF->listarUF($rsUF);

// HIDDENS
$obHdnAcao  = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setId       ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl  = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

// COMPONENTES
$obTxtProfissao = new TextBox;
$obTxtProfissao->setRotulo        ( "Profissão"                         );
$obTxtProfissao->setTitle         ( "Profissão do Respónsavel Técnico"  );
$obTxtProfissao->setName          ( "inCodigoProfissao"                 );
$obTxtProfissao->setValue         ( $_REQUEST["inCodigoProfissao"]      );
$obTxtProfissao->setSize          ( 8                                   );
$obTxtProfissao->setMaxLength     ( 8                                   );
$obTxtProfissao->setNull          ( true                               );
$obTxtProfissao->setInteiro       ( true                                );
$obTxtProfissao->setId                ( "inCodigoProfissao" );
$obTxtProfissao->obEvento->setOnChange("montaAtributosProfissao();"     );

$obCmbProfissao = new Select;
$obCmbProfissao->setName          ( "cmbProfissao"                      );
$obCmbProfissao->setValue         ( $_REQUEST["inCodigoProfissao"]      );
$obCmbProfissao->addOption        ( "", "Selecione"                     );
$obCmbProfissao->setCampoId       ( "cod_profissao"                     );
$obCmbProfissao->setCampoDesc     ( "nom_profissao"                     );
$obCmbProfissao->preencheCombo    ( $rsProfissoes                       );
$obCmbProfissao->setNull          ( true                               );
$obCmbProfissao->setStyle         ( "width: 220px"                      );
$obCmbProfissao->obEvento->setOnChange("montaAtributosProfissao();"     );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo    ( "CGM"                             );
$obBscCGM->setTitle     ( "Busca profissional no CGM"       );
$obBscCGM->setId        ( "inNomCGM"                        );
$obBscCGM->setNull      ( true                             );
$obBscCGM->obCampoCod->setName  ( "inNumCGM"    );
$obBscCGM->obCampoCod->setValue ( $_REQUEST["inNumCGM"]  );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGMPF');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','inNomCGM','fisica','".Sessao::getId()."','800','550');" );

$obTxtNomeRegistro = new TextBox;
$obTxtNomeRegistro->setRotulo        ( "<span id='rotRegistro'>Registro</span>"    );
$obTxtNomeRegistro->setTitle         ( "Numero do registro no conselho de classe " );
$obTxtNomeRegistro->setName          ( "stRegistro"         );
$obTxtNomeRegistro->setId            ( "stRegistro"         );
$obTxtNomeRegistro->setSize          ( 10                   );
$obTxtNomeRegistro->setMaxLength     ( 10                   );
$obTxtNomeRegistro->setNull          ( true                );

$obTxtUf = new TextBox;
$obTxtUf->setRotulo        ( "UF"                               );
$obTxtUf->setTitle         ( "Estado Correspondente ao conselho de classe"  );
$obTxtUf->setName          ( "inCodigoUf"                       );
$obTxtUf->setValue         ( $_REQUEST["codUF"]                 );
$obTxtUf->setSize          ( 8                                  );
$obTxtUf->setMaxLength     ( 2                                  );
$obTxtUf->setNull          ( true                              );
$obTxtUf->setInteiro       ( true                               );
$obTxtUf->obEvento->setOnChange("montaAtributosUf();"           );

$obCmbUf = new Select;
$obCmbUf->setName          ( "cmbUf"                    );
$obCmbUf->setValue         ( $_REQUEST["codUF"]         );
$obCmbUf->addOption        ( "", "Selecione"            );
$obCmbUf->setCampoId       ( "cod_uf"                   );
$obCmbUf->setCampoDesc     ( "nom_uf"                   );
$obCmbUf->preencheCombo    ( $rsUF                      );
$obCmbUf->setNull          ( true                      );
$obCmbUf->setStyle         ( "width: 220px"             );
$obCmbUf->obEvento->setOnChange("montaAtributosUf();"   );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                           );
$obFormulario->setAjuda     ( "UC-05.02.04"                        );
$obFormulario->addTitulo    ( "Dados para Respónsavel Técnico"  );
$obFormulario->addHidden    ( $obHdnCtrl                        );
$obFormulario->addHidden    ( $obHdnAcao                        );
/*$obFormulario->addHidden    ( $obHdnNomeLogradouro      );
$obFormulario->addHidden    ( $obHdnLote                );*/

$obFormulario->addComponenteComposto($obTxtProfissao,$obCmbProfissao);
$obFormulario->addComponente        ( $obBscCGM                 );
$obFormulario->addComponente        ( $obTxtNomeRegistro        );
$obFormulario->addComponenteComposto($obTxtUf,$obCmbUf          );
$obFormulario->setFormFocus ( $obTxtProfissao->getid() );

$obFormulario->OK();
$obFormulario->show();

?>

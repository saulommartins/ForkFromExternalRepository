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
    * Página de processamento oculto para o cadastro de corretagem
    * Data de Criação   : 25/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodriguesa

    * @ignore

    * $Id: OCManterCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.8  2007/05/09 19:07:22  cercato
Bug #8969#

Revision 1.7  2007/04/10 14:08:26  rodrigo
Bug #8969#

Revision 1.6  2006/09/18 10:30:25  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php"    );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php" );

switch ($_REQUEST ["stCtrl"]) {
    case "atualizaFiltro":
        if ($_REQUEST["boTipoCorretagem"] == "imobiliaria") {
            $tipoCGM        = "juridica";
            $textCGM        = "da imobiliária (pessoa jurídica)";
            $textCRECI      = "da imobiliária";
            $tipoBuscaCreci = "imobiliaria";
        } elseif ($_REQUEST["boTipoCorretagem"] == "corretor") {
            $tipoCGM        = "fisica";
            $textCGM        = "do corretor (pessoa física)";
            $textCRECI      = "do corretor";
            $tipoBuscaCreci = "corretor";
        }
        $obBscCreci = new BuscaInner;
        $obBscCreci->setRotulo                ( "CRECI"                                          );
        $obBscCreci->setTitle                 ( "Número do registro no CRECI $textCRECI"         );
        $obBscCreci->setNull                  ( true                                             );
        $obBscCreci->setId                    ( "stNomeResponsavel"                              );
        $obBscCreci->obCampoCod->setName      ( "stCreciResponsavel"                             );
        $obBscCreci->obCampoCod->setInteiro   ( false                                            );
        $obBscCreci->obCampoCod->setSize      ( 10                                               );
        $obBscCreci->obCampoCod->setMaxLength ( 10                                               );
        $obBscCreci->obCampoCod->setValue     ( $_REQUEST["stCreciResponsavel"]                  );
        $obBscCreci->obCampoCod->obEvento->setOnChange("buscaDado('buscaCreci');"                );
        $obBscCreci->setFuncaoBusca("abrePopUp('../popups/corretagem/FLProcurarCorretagem.php','frm','stCreciResponsavel'
                                     ,'stNomeResponsavel','".$tipoBuscaCreci."','".Sessao::getId()."','800','550')" );

        $obFormulario = new Formulario;
        $obBscCGM = new BuscaInner;
        $obBscCGM->setRotulo              ( "CGM"                          );
        $obBscCGM->setTitle               ( "CGM $textCGM"                 );
        $obBscCGM->setNull                ( true                           );
        $obBscCGM->setId                  ( "campoInner"                   );
        $obBscCGM->obCampoCod->setName    ( "inNumCGM"                     );
        $obBscCGM->obCampoCod->setValue   ( $_REQUEST["inNumCGM"]          );
        $obBscCGM->obCampoCod->obEvento->setOnChange("buscaCGM('$tipoCGM');" );
        $obBscCGM->setFuncaoBusca("abrePopUp('../popups/cgm/FLProcurarCgm.php','frm','inNumCGM'
                                   ,'campoInner','".$tipoCGM."','".Sessao::getId()."','800','550')" );

        $obFormulario->addComponente      ( $obBscCreci                    );
        $obFormulario->addComponente      ( $obBscCGM                      );
        $obFormulario->montaInnerHTML();
        $stJs  = "d.getElementById('spnCGM').innerHTML = '".$obFormulario->getHTML()."';\n";
        $stJs .= "f.stCreciResponsavel.value = '';\n";
        $stJs .= "f.inNumCGM.value = '';\n";
        $stJs .= 'd.getElementById("stNomeResponsavel").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
        $stJs .= "f.tipoBuscaCreci.value = '$tipoBuscaCreci'\n";
    break;
    case "validaCreci":
        $obRCIMCorretagem = new RCIMCorretagem;
        $obRCIMCorretagem->setRegistroCreci( $_REQUEST["stRegistroCreci"]);
        $obRCIMCorretagem->listarCorretagem ( $rsCorretagem );

        if ( $rsCorretagem->getNumLinhas() > 0 ) {
            $stJs  = 'f.stRegistroCreci.value = "";';
            $stJs .= 'f.stRegistroCreci.focus();';
            $stJs .= "erro = true;\n";
            $stJs .= "mensagem += 'Registro Creci já cadastrado no sistema! (".$rsCorretagem->getCampo("creci").")';\n";
            $stJs .= "SistemaLegado::alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');\n";
        }
    break;
    case "buscaCreci":
        if ($_REQUEST["tipoBuscaCreci"] == "corretor") {
            $obRCIMCorretor = new RCIMCorretor;
            $obRCIMCorretor->setRegistroCreci( $_REQUEST["stCreciResponsavel"]);
            $obRCIMCorretor->listarCorretores ( $rsCorretagem );
        } elseif ($_REQUEST["tipoBuscaCreci"] == "imobiliaria") {
            $obRCIMImobiliaria = new RCIMImobiliaria( new RCIMCorretor );
            $obRCIMImobiliaria->setRegistroCreci( $_REQUEST["stCreciResponsavel"]);
            $obRCIMImobiliaria->listarImobiliarias ( $rsCorretagem );
        }

        if ( $rsCorretagem->eof() ) {
            $stJs  = 'f.stCreciResponsavel.value = "";';
            $stJs .= 'f.stCreciResponsavel.focus();';
            $stJs .= 'd.getElementById("stNomeResponsavel").innerHTML = "&nbsp;";';
            $stJs .= "erro = true;\n";
            $stJs .= "mensagem += 'Código CRECI inválido (".$_REQUEST['stCreciResponsavel'].").';\n";
            $stJs .= "alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');\n";
        } else {
            $stJs  = 'd.getElementById("stNomeResponsavel").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
        }
    break;
    case "buscaCGM":
        if ($_POST[ 'inNumCGM' ] != '') {
            if ($_REQUEST["tipoCGM"] == "juridica") {
              $msgAviso = "Pessoa Jurídica";
              $obRCGMPessoaJuridica = new RCGMPessoaJuridica;
              $obRCGMPessoaJuridica->setNumCGM( $_POST[ 'inNumCGM' ] );
              $obRCGMPessoaJuridica->consultarCGM( $rsCGM );
            } elseif ($_REQUEST["tipoCGM"] == "fisica") {
              $msgAviso = "Pessoa Física";
              $obRCGMPessoaFisica = new RCGMPessoaFisica;
              $obRCGMPessoaFisica->setNumCGM( $_POST[ 'inNumCGM' ] );
              $obRCGMPessoaFisica->consultarCGM( $rsCGM );
            }
            $inNumLinhas = $rsCGM->getNumLinhas();
            if ($inNumLinhas <= 0) {
              $stJs  = 'f.inNumCGM.value = "";';
              $stJs .= 'f.inNumCGM.focus();';
              $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            //$stJs .= "alertaAviso('@O CGM informado não pertence a uma ".$msgAviso.".(".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
              $stJs .= "alertaAviso('@O CGM informado deve pertencer a uma ".$msgAviso." (".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs  = 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
            }
        }
    break;
}
if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}
?>

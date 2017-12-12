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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Classe de Regra de Relatorios
    * Data de Criação   : 06/08/2004
    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

class RRelatorio
{
var $obTransacao;
var $obTImpressora;
var $obTUsuarioImpressora;
var $obTConfiguracao;
var $inNumCGM;
var $stFilaImpressao;
var $stNomImpressora;
var $obTCandidato;
var $inExercicio;
//LOGOTIPO DAS ENTIDADES
var $inCodigoEntidade;
var $stExercicioEntidade;

function setNumCGM($valor) { $this->inNumCGM           = $valor; }
function setExercicio($valor) { $this->inExercicio        = $valor; }
function setFilaImpressao($valor) { $this->stFilaImpressao    = $valor; }
function setNomImpressora($valor) { $this->stNomImpressora    = $valor; }
function setCodigoEntidade($valor) { $this->inCodigoEntidade   = $valor; }
function setExercicioEntidade($valor) { $this->stExercicioEntidade = $valor; }

function getNumCGM() { return $this->inNumCGM;        }
function getExercicio() { return $this->inExercicio;     }
function getFilaImpressao() { return $this->stFilaImpressao; }
function getNomImpressora() { return $this->stNomImpressora; }
function getCodigoEntidade() { return $this->inCodigoEntidade;}
function getExercicioEntidade() { return $this->stExercicioEntidade; }

function RRelatorio()
{
    include_once( CLA_TRANSACAO );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoImpressora.class.php" );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuarioImpressora.class.php" );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $this->obTransacao = new Transacao;
    $this->obTImpressora = new TAdministracaoImpressora;
    $this->obTUsuarioImpressora = new TAdministracaoUsuarioImpressora;
    $this->obTConfiguracao = new TAdministracaoConfiguracao;
}

function listarImpressoraUsuario(&$rsRecordSet, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = "";
        if ($this->inNumCGM) {
            $stFiltro = " AND us.numcgm = ".$this->inNumCGM;
        }
        $obErro = $this->obTUsuarioImpressora->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCandidato);
    }

    return $obErro;
}

function consultarImpressoraPadrao($rsRecordSet, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = new Erro;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = "";
        if ($this->inNumCGM) {
            $stFiltro = " AND us.numcgm = ".$this->inNumCGM;
        }
        $obErro = $this->obTUsuarioImpressora->recuperaImpressoraPadrao( $rsRecordSet, $stFiltro, "", $boTransacao );
        if (!$obErro->ocorreu()) {
            $this->setFilaImpressao( $rsRecordSet->getCampo('fila_impressao') );
            $this->setNomImpressora( $rsRecordSet->getCampo('nom_impressora') );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCandidato);
    }

    return $obErro;
}

function executaFrameOculto($stArquivo)
{
    $stHTML  = " <html> \n";
    $stHTML .= " <head> \n";
    $stHTML .= " <script type=\"text/javascript\"> \n";
    $stHTML .= "     function executa() { \n";
    $stHTML .= "         var mensagem = \"\"; \n";
    $stHTML .= "         var erro = false; \n";
    $stHTML .= "         var f = window.parent.frames[\"telaPrincipalRelatorio\"].document.frm; \n";
    $stHTML .= "         var d = window.parent.frames[\"telaPrincipalRelatorio\"].document; \n";
    $stHTML .= "         var aux; \n";
    $stHTML .= "         window.open('".CAM_FW_POPUPS."relatorio/Concluido.php?".Sessao::getId()."','telaPrincipalRelatorio'); \n";
    $stHTML .= "         window.open('".$stArquivo."?".Sessao::getId()."','ocultoRelatorio'); \n";
    $stHTML .= "         if (erro) alertaAviso(mensagem,\"form\",\"erro\",\"".Sessao::getId()."\"); \n";
    $stHTML .= "     } \n";
    $stHTML .= " </script> \n";
    $stHTML .= " </head> \n";
    $stHTML .= " <body onLoad=\"javascript:executa();\"> \n";
    $stHTML .= " </body> \n";
    $stHTML .= " </html> \n";
    echo $stHTML;
}

function recuperaCabecalho(&$arConfiguracao)
{
    global $request;
    include_once(CLA_MASCARA_CNPJ);
    $obMascaraCNPJ  = new MascaraCNPJ;
    $obTAcao        = new TAdministracaoAcao;
    $boTransacao    = new Transacao;

    $arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "", "logradouro" => "", "numero" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "" );
    $this->obTConfiguracao->setDado( "cod_modulo", 2 );
    if ( empty($this->inExercicio) ) {

        $this->inExercicio = Sessao::getExercicio();
    }
    $this->obTConfiguracao->setDado( "exercicio" , $this->inExercicio    );
    foreach ($arPropriedades as $stParametro => $stValor) {
        $obErro = $this->obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
        $arConfiguracao[$stParametro] = $stValor;
        if ( $obErro->ocorreu() ) {
            break;
        }
    }
    $obMascaraCNPJ->mascaraDado( $arConfiguracao['cnpj'] );

    if ( $this->getCodigoEntidade() and $this->getExercicioEntidade() ) {
        if ( is_file(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidadeLogotipo.class.php" ) ) {
            include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidadeLogotipo.class.php" );
            $obTOrcamentoBrasao = new TOrcamentoEntidadeLogotipo;
            $obTOrcamentoBrasao->setDado( 'exercicio', $this->getExercicioEntidade() );
            $obTOrcamentoBrasao->setDado( 'cod_entidade', $this->getCodigoEntidade() );
            $obErro = $obTOrcamentoBrasao->recuperaPorChave( $rsBrasao );
            if ( !$obErro->ocorreu() and !$rsBrasao->eof() and file_exists(  CAM_GF_ORCAMENTO.'anexos/'.$rsBrasao->getCampo( 'logotipo' ) ) ) {
                $arConfiguracao['logotipo'] =  CAM_GF_ORCAMENTO.'anexos/'.$rsBrasao->getCampo( 'logotipo' );
            } else {
                $arConfiguracao['logotipo'] = CAM_FW_IMAGENS.$arConfiguracao['logotipo'];
            }
            if ( is_file(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" ) ) {
                include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
                $obTOrcamentoEntidade = new TOrcamentoEntidade;
                $obTOrcamentoEntidade->setDado( 'exercicio', $this->getExercicioEntidade() );
                $obTOrcamentoEntidade->setDado( 'cod_entidade', $this->getCodigoEntidade() );
                $obErro = $obTOrcamentoEntidade->recuperaPorChave( $rsEntidade );
                if ( !$obErro->ocorreu() and !$rsEntidade->eof() ) {
                    include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
                    $obTMapCGM = new TCGM;
                    $obTMapCGM->setDado("numcgm", $rsEntidade->getCampo("numcgm") );
                    $obErro = $obTMapCGM->recuperaPorChave( $rsCGM );
                    if ( !$obErro->ocorreu() and !$rsCGM->eof() ) {
                        $arConfiguracao["nom_prefeitura"] = $rsCGM->getCampo("nom_cgm");
                        $arConfiguracao["fone"]           = $rsCGM->getCampo("fone_residencial");
                        $arConfiguracao["fax"]            = $rsCGM->getCampo("fone_comercial");
                        $arConfiguracao["e_mail"]         = $rsCGM->getCampo("e_mail");
                        $arConfiguracao["logradouro"]     = $rsCGM->getCampo("tipo_logradouro")." ".$rsCGM->getCampo("logradouro");
                        $arConfiguracao["numero"]         = $rsCGM->getCampo("numero")." ".$rsCGM->getCampo("complemento");
                        $arConfiguracao["bairro"]         = $rsCGM->getCampo("bairro");
                        include_once( CAM_FW_MASCARA."MascaraCEP.class.php" );
                        $obMascaraCEP = new MascaraCEP;
                        $arConfiguracao["cep"]            = $rsCGM->getCampo("cep");
                        $obMascaraCEP->mascaraDado( $arConfiguracao["cep"] );
                        $obErro =  $obTMapCGM->recuperarelacionamentosintetico( $rsCGMCNPJ, "and CGM.numcgm = ".$rsEntidade->getCampo("numcgm") );
                        if ( !$obErro->ocorreu() and !$rsCGMCNPJ->eof() ) {
                            include_once( CAM_FW_MASCARA."MascaraCNPJ.class.php" );
                            $obMascaraCNPJ = new MascaraCNPJ;
                            $arConfiguracao["cnpj"] = $rsCGMCNPJ->getCampo("cnpj");
                            $obMascaraCNPJ->mascaraDado( $arConfiguracao["cnpj"] );
                        }
                        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
                        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
                        $obTMunicipio = new TMunicipio();
                        $obTUF = new TUF();
                        $obTMunicipio->setDado("cod_municipio",$rsCGM->getCampo("cod_municipio"));
                        $obTMunicipio->setDado("cod_uf",$rsCGM->getCampo("cod_uf"));
                        $obTMunicipio->recuperaPorChave($rsMunicipio);
                        $obTUF->setDado("cod_uf",$rsCGM->getCampo("cod_uf"));
                        $obTUF->recuperaPorChave($rsUF);

                        $arConfiguracao["nom_municipio"] = $rsMunicipio->getCampo("nom_municipio");
                        $arConfiguracao["sigla_uf"] = $rsUF->getCampo("sigla_uf");
                    }
                }
            }
        }
    }

    $stFiltro = " AND A.cod_acao = ".Sessao::read('acao');
    $obErro = $obTAcao->recuperaRelacionamento( $rsRecordSet, $stFiltro, ' A.cod_acao ', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arConfiguracao[ "nom_modulo" ]         = $rsRecordSet->getCampo( "nom_modulo" );
        $arConfiguracao[ "nom_funcionalidade" ] = $rsRecordSet->getCampo( "nom_funcionalidade" );
        $arConfiguracao[ "nom_acao" ]           = $rsRecordSet->getCampo( "nom_acao" );
    }

    return $obErro;
}

}
?>

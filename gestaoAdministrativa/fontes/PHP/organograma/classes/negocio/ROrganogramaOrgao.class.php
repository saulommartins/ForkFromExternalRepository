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
* Classe de negócio OrganogramaOrgao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 29032 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-07 10:41:35 -0300 (Seg, 07 Abr 2008) $

Casos de uso: uc-01.05.02, uc-04.05.40, 04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php"     );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php"           );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgaoDescricao.class.php"  );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgaoNivel.class.php"      );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."VOrganogramaOrgaoNivel.class.php"      );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgaoCgm.class.php"        );
include_once ( CAM_GA_ORGAN_MAPEAMENTO."FOrganogramaVisualizarPopUp.class.php" );
include_once ( CAM_GA_ORGAN_NEGOCIO   ."ROrganogramaNivel.class.php"           );
include_once ( CAM_GRH_CAL_NEGOCIO    ."RCalendario.class.php"                 );
include_once ( CAM_GA_CGM_NEGOCIO     ."RCGMPessoaFisica.class.php"            );
include_once ( CAM_GA_CGM_NEGOCIO     ."RCGMPessoaJuridica.class.php"          );
include_once ( CAM_GA_ORGAN_NEGOCIO   ."ROrganogramaOrganograma.class.php"     );
include_once ( CAM_GA_NORMAS_NEGOCIO  ."RNorma.class.php"                      );

class ROrganogramaOrgao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodOrgaoEstruturado;
/**
    * @access Private
    * @var Integer
*/
var $inCodOrgaoReduzido;
/**
    * @access Private
    * @var Integer
*/
var $inCodOrgao;
/**
    * @access Private
    * @var Integer
*/
var $inCodOrgaoSuperior;
/**
    * @access Private
    * @var String
*/
var $stSigla;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $stUltimaDescricao;
/**
    * @access Private
    * @var String
*/
var $dtCriacao;
/**
    * @access Private
    * @var String
*/
var $dtInativacao;
/**
    * @access Private
    * @var Object
*/
var $obRCgmPF;
/**
    * @access Private
    * @var Object
*/
var $obRCgmPJ;
/**
    * @access Private
    * @var Object
*/
var $obRCalendario;
/**
    * @access Private
    * @var Object
*/
var $obRNivel;
/**
    * @access Private
    * @var Object
*/
var $obROrganograma;
/**
    * @access Private
    * @var Object
*/
var $obRNorma;
/**
    * @access Private
    * @var Object
*/
var $obTOrgao;
/**
    * @access Private
    * @var Object
*/
var $obTOrgaoDescricao;
/**
    * @access Private
    * @var Object
*/
var $obTOrgaoNivel;
/**
    * @access Private
    * @var Object
*/
var $obTOrgaoCgm;
/**
    * @access Private
    * @var Object
*/
var $obUltimoNivel;
/**
    * @access Private
    * @var Object
*/
var $obVOrgaoNivel;
/**
    * @access Private
    * @var Object
*/
var $obTOrgaoNivelPopUp;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var DATE
*/
var $dtVigencia;
/**
    * @access Private
    * @var STRING
*/
var $stCodEstrutural;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrgaoEstruturado($valor) { $this->inCodOrgaoEstruturado = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrgaoReduzido($valor) { $this->inCodOrgaoReduzido   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrgao($valor) { $this->inCodOrgao            = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrgaoSuperior($valor) { $this->inCodOrgaoSuperior    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSigla($valor) { $this->stSigla               = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setUltimaDescricao($valor) { $this->stUltimaDescricao     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCriacao($valor) { $this->stCriacao             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setInativacao($valor) { $this->stInativacao          = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setUltimoNivel($valor) { $this->obUltimoNivel       = $valor; }
/**
     * @access Public
     * @param Array $valor
*/
function setNivel($valor) { $this->arNivel             = $valor;  }
/**
    * @access Public
    * @param Object $valor
*/
function setTOrgao($valor) { $this->obTOrgao            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTOrgaoNivel($valor) { $this->obTOrgaoNivel       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTOrgaoDescricao($valor) { $this->obTOrgaoDescricao    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setVOrgaoNivel($valor) { $this->obVOrgaoNivel       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTOrgaoNivelPopUp($valor) { $this->obTOrgaoNivelPopUp   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTOrgaoCgm($valor) { $this->obTOrgaoCgm         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCalendario($valor) { $this->obRCalendario       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRNivel($valor) { $this->obRNivel            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCgmPF($valor) { $this->obRCgmPF            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCgmPJ($valor) { $this->obRCgmPJ            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setROrganograma($valor) { $this->obROrganograma      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRNorma($valor) { $this->obRNorma            = $valor; }
/**
    * @access Public
    * @param DATE $valor
*/
function setVigencia($valor) { $this->dtVigencia          = $valor; }
/**
    * @access Public
    * @param STRING $valor
*/
function setCodEstrutural($valor) { $this->stCodEstrutural          = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodOrgaoEstruturado() { return $this->inCodOrgaoEstruturado ; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrgaoReduzido() { return $this->inCodOrgaoReduzido ; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrgao() { return $this->inCodOrgao            ; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrgaoSuperior() { return $this->inCodOrgaoSuperior    ; }
/**
    * @access Public
    * @return String
*/
function getSigla() { return $this->stSigla               ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao           ; }
/**
    * @access Public
    * @return String
*/
function getUltimaDescricao() { return $this->stUltimaDescricao     ; }
/**
    * @access Public
    * @return String
*/
function getCriacao() { return $this->stCriacao             ; }
/**
    * @access Public
    * @return String
*/
function getInativacao() { return $this->stInativacao          ; }
/**
     * @access Public
     * @return Object
*/
function getUltimoNivel() { return $this->obUltimoNivel       ; }
/**
     * @access Public
     * @return Array
*/
function getNivel() { return $this->arNivel             ;  }
/**
    * @access Public
    * @return Object
*/
function getTOrgao() { return $this->obTOrgao            ; }
/**
    * @access Public
    * @return Object
*/
function getTOrgaoDescricao() { return $this->obTOrgaoDescricao   ; }
/**
    * @access Public
    * @return Object
*/
function getTOrgaoNivel() { return $this->obTOrgaoNivel       ; }
/**
    * @access Public
    * @return Object
*/
function getVOrgaoNivel() { return $this->obVOrgaoNivel       ; }
/**
    * @access Public
    * @return Object
*/
function getTOrgaoNivelPopUp() { return $this->obTOrgaoNivelPopUp     ; }
/**
    * @access Public
    * @return Object
*/
function getTOrgaoCgm() { return $this->obTOrgaoCgm         ; }
/**
    * @access Public
    * @return Object
*/
function getRCalendario() { return $this->obRCalendario       ; }
/**
    * @access Public
    * @return Object
*/
function getRNivel() { return $this->obRNivel            ; }
/**
    * @access Public
    * @return Object
*/
function getRCgmPF() { return $this->obRCgmPF            ; }
/**
    * @access Public
    * @return Object
*/
function getRCgmPJ() { return $this->obRCgmPJ            ; }
/**
    * @access Public
    * @return Object
*/
function getROrganograma() { return $this->obROrganograma      ; }
/**
    * @access Public
    * @return Object
*/
function getRNorma() { return $this->obRNorma            ; }
/**
    * @access Public
    * @return Date
*/
function getVigencia() { return $this->dtVigencia          ; }
/**
    * @access Public
    * @return String
*/
function getCodEstrutural() { return $this->stCodEstrutural     ; }

/**
     * Método construtor
     * @access Private
*/
function ROrganogramaOrgao()
{
    $this->setTOrgao           ( new TOrganogramaOrgao           );
    $this->setTOrgaoDescricao  ( new TOrganogramaOrgaoDescricao  );
    $this->setVOrgaoNivel      ( new VOrganogramaOrgaoNivel      );
    $this->setTOrgaoNivelPopUp ( new FOrganogramaVisualizarPopUp );
    $this->setTOrgaoNivel      ( new TOrganogramaOrgaoNivel      );
    $this->setTOrgaoCgm        ( new TOrganogramaOrgaoCgm        );
    $this->setRCalendario      ( new RCalendario                 );
    $this->setRNivel           ( new ROrganogramaNivel           );
    $this->setROrganograma     ( new ROrganogramaOrganograma     );
    $this->setRCgmPF           ( new RCGMPessoaFisica            );
    $this->setRCgmPJ           ( new RCGMPessoaJuridica          );
    $this->setRNorma           ( new RNorma                      );
    $this->obTransacao         = new Transacao;
    $this->arNivel             = array();
}

/**
    * Instancia um novo objeto do tipo Nivel
    * @access Public
*/
function addNivel()
{
    $this->setUltimoNivel( new ROrganogramaNivel );
}
/**
    * Adiciona o objeto do tipo Nivel ao array
    * @access Public
*/
function commitNivel()
{
    $arElementos   = $this->getNivel();
    $arElementos[] = $this->getUltimoNivel();
    $this->setNivel( $arElementos );
}
/**
    * Salva dados de Organograma no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->getCodOrgao() ) {
            $obErro = $this->alterar( $boTransacao );
        } else {
            // SETA DADOS PARA TABELA DE ORGAO
            $this->obTOrgao->setDado("cod_organograma", $this->obROrganograma->getCodOrganograma() );
            $this->obTOrgao->setDado("sigla_orgao"    , $this->getSigla() );
            $this->obTOrgao->setDado("criacao"        , $this->getCriacao() );
            $this->obTOrgao->setDado("cod_calendar"   , $this->obRCalendario->getCodCalendar() );
            $this->obTOrgao->setDado("num_cgm_pf"     , $this->obRCgmPF->getNumCGM() );
            $this->obTOrgao->setDado("cod_norma"      , $this->obRNorma->getCodNorma() );

            // CASO SEJA INCLUSAO DE NOVO ORGAO
            $this->obTOrgao->proximoCod( $inCodOrgao , $boTransacao );
            $this->setCodOrgao( $inCodOrgao );
            $this->obTOrgao->setDado("cod_orgao" , $this->getCodOrgao() );
            $stOperacao = 'inclusao';
            $obErro = $this->obTOrgao->inclusao( $boTransacao );
            if (!$obErro->ocorreu()) {

                $this->obTOrgaoDescricao->setDado("cod_orgao" , $this->getCodOrgao() );
                $this->obTOrgaoDescricao->setDado("timestamp" , date('Y-m-d H:i:s') );
                $this->obTOrgaoDescricao->setDado("descricao" , $this->getDescricao());
                $obErro = $this->obTOrgaoDescricao->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {

                    $obErro = $this->obROrganograma->listarNiveis( $rsNiveis, 'cod_nivel', $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->listarValoresSuperior( $rsValores, 'cod_nivel', $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            if ($this->inCodOrgaoSuperior) {
                                $obErro = $this->recuperaUltimoValor( $inValor, $boTransacao );
                                $obErro = $this->recuperaUltimoValor( $inUltValor, $boTransacao );
                            } else {
                                $obErro = $this->recuperaUltimoValorNivelPai ( $inUltValor, $boTransacao );
                                $inCodSupTEMP = $this->inCodOrgaoSuperior ;
                                $this->inCodOrgaoSuperior = $inUltValor;
                                $obErro = $this->listarValoresSuperior( $rsValores, 'cod_nivel', $boTransacao );
                                $this->inCodOrgaoSuperior = $inCodSupTEMP;
                            }

                            // INSERE N REGISTROS NA TABELA DE ORGAO_NIVEL ( N = NUMERO DE NIVEIS )

                            while ( !$rsNiveis->eof() && (!$obErro->ocorreu()) ) {
                                $inValor = $rsValores->getCampo('valor');
                                $inValor = ($inValor) ? $inValor : '0';

                                if ( $this->obRNivel->getCodNivel() == $rsNiveis->getCampo('cod_nivel') ) {

                                    if ($this->obRNivel->getCodNivel() == "1") {
                                        $boTrocouRaiz = true;
                                    }

                                    $inValor = ++$inUltValor;

                                }

                                $this->obTOrgaoNivel->setDado("cod_orgao"       , $this->getCodOrgao() );
                                $this->obTOrgaoNivel->setDado("cod_organograma" , $this->obROrganograma->getCodOrganograma() );
                                $this->obTOrgaoNivel->setDado("cod_nivel"       , $rsNiveis->getCampo('cod_nivel') );

                                if ($boTrocouRaiz and $rsNiveis->getCampo('cod_nivel') <> "1") {
                                    $this->obTOrgaoNivel->setDado("valor"           , "0" );
                                } else {
                                    $this->obTOrgaoNivel->setDado("valor"           , $inValor );
                                }
                                $obErro = $this->obTOrgaoNivel->inclusao( $boTransacao );
                                if( $obErro->ocorreu() )
                                    break;

                                $rsNiveis ->proximo();
                                $rsValores->proximo();
                            }

                            $boTrocouRaiz = false;
                         }
                    }
                }
            }
            //-----------------
        }
        if ( !$obErro->ocorreu() && $this->obRCgmPJ->getNumCGM() ) {
            if ($stOperacao=='inclusao') {
                    $this->obTOrgaoCgm->setDado("cod_orgao"         , $this->getCodOrgao() );
                    $this->obTOrgaoCgm->setDado("numcgm"            , $this->obRCgmPJ->getNumCGM() );
                    $this->obTOrgaoCgm->setDado("cod_organograma"   , $this->obROrganograma->getCodOrganograma() );
                    $obErro = $this->obTOrgaoCgm->inclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}

function alterar($boTransacao = "")
{
    $this->obTOrgao->setDado("cod_organograma", $this->obROrganograma->getCodOrganograma() );
    $this->obTOrgao->setDado("sigla_orgao"    , $this->getSigla()                          );
    $this->obTOrgao->setDado("criacao"        , $this->getCriacao()                        );
    $this->obTOrgao->setDado("cod_calendar"   , $this->obRCalendario->getCodCalendar()     );
    $this->obTOrgao->setDado("num_cgm_pf"     , $this->obRCgmPF->getNumCGM()               );
    $this->obTOrgao->setDado("cod_norma"      , $this->obRNorma->getCodNorma()             );
    $this->obTOrgao->setDado("cod_orgao"      , $this->getCodOrgao()                       );
    $obErro = $this->obTOrgao->alteracao( $boTransacao );

    if (!$obErro->ocorreu()) {

        # Caso tenha sido alterado a descrição do órgão, inclui novo registro
        # na tabela organograma.orgao_descricao
        if ($this->getDescricao() != $this->getUltimaDescricao()) {
            $this->obTOrgaoDescricao->setDado("cod_orgao" , $this->getCodOrgao()  );
            $this->obTOrgaoDescricao->setDado("timestamp" , date('Y-m-d H:i:s'));
            $this->obTOrgaoDescricao->setDado("descricao" , $this->getDescricao() );
            $obErro = $this->obTOrgaoDescricao->inclusao( $boTransacao );
        }

        # Não é necessário modificar os níveis do órgão no formulário de alteração,
        # já que essas informações vem como Label na tela e não podem ser alteradas.

        /*
        //Verifica se foi alterado o Órgão Superior
        $stFiltro  = " WHERE cod_organograma = ".$this->obROrganograma->getCodOrganograma();
        $stFiltro .= " AND   cod_orgao       = ".$this->getCodOrgao();
        $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsOrgaoNivel, $stFiltro, "cod_nivel", $boTransacao );

        if (!$obErro->ocorreu()) {
            $obErro = $this->retornaNivelOrgao( $arNivelOrgao, $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $inCountOrgaoNivel = count($arNivelOrgao);
                while ( !$rsOrgaoNivel->eof() ) {
                   if ( $inCountOrgaoNivel == $rsOrgaoNivel->getCorrente() ) {
                      break;
                   }
                   $arValores[] = $rsOrgaoNivel->getCampo('valor');
                   $rsOrgaoNivel->proximo();
                }
                $inCodOrgaoSuperior = $this->consultarOrgaoSuperior( $arValores , $in, $boTransacao );
                if ($inCodOrgaoSuperior != $this->inCodOrgaoSuperior) {
                    $this->obROrganograma->setCodOrganograma( $this->obROrganograma->getCodOrganograma() );
                    $this->obROrganograma->listarNiveis( $rsNiveis, 'cod_nivel', $boTransacao );
                    $this->listarValoresSuperior( $rsValores, 'cod_nivel', $boTransacao );
                    $this->obRNivel->setCodNivel( ($this->obRNivel->getCodNivel()) ? $this->obRNivel->getCodNivel() : 1 );
                    //Recupera o último valor do nível existente
                    $obErro = $this->recuperaUltimoValor( $inUltValor, $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        return $obErro;
                    }
                    //Armazena os valores para serem preservados
                    $inCodOrgaoAnt      = $this->getCodOrgao();
                    $inCodOrganogramaAnt= $this->obROrganograma->getCodOrganograma();
                    $this->listarOrgaosInferiores( $rsOrgaosInferiores, '', $boTransacao);

                    //Identifica quem são os órgãos superiores de cada órgão abaixo e armazena em um array';
                    while (!$rsOrgaosInferiores->eof()) {
                        $arValores          = array();
                        $inCodOrganograma   = $rsOrgaosInferiores->getCampo('cod_organograma');
                        $inCodOrgao         = $rsOrgaosInferiores->getCampo('cod_orgao');

                        //$this->obROrganograma->setCodOrganograma( $inCodOrganograma );
                        $this->setCodOrgao                      ( $inCodOrgao );

                        $stFiltro  = " WHERE cod_organograma = ".$inCodOrganograma;
                        $stFiltro .= " AND   cod_orgao       = ".$inCodOrgao;

                        $stFiltro = " WHERE cod_orgao       = ".$inCodOrgao;
                        $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsOrgaoNivel, $stFiltro, "cod_nivel", $boTransacao );

                        while ( !$rsOrgaoNivel->eof() ) {
                            if( (count($this->retornaNivelOrgao($boTransacao)))==$rsOrgaoNivel->getCorrente() )
                                break;
                            $arValores[] = $rsOrgaoNivel->getCampo('valor');
                            $rsOrgaoNivel->proximo();
                        }

                        $arOrgaosSuperiores["$inCodOrganograma-$inCodOrgao"] = $this->consultarOrgaoSuperior( $arValores , $boTransacao );
                        //echo "<i> --".$arOrgaosSuperiores["$inCodOrganograma-$inCodOrgao"]."-- </i><br>";
                        $rsOrgaosInferiores->proximo();
                    }

                    //Retorna com os valores originais
                    $this->setCodOrgao( $inCodOrgaoAnt );
                    $this->obROrganograma->setCodOrganograma( $inCodOrganogramaAnt );

                    //Efetua alteração do órgão selecionado.

                    # Não tem necessidade de alterar níveis do órgão, já que
                    # no formulário de alteração, essas informações são carregadas como label.

                    while ( !$rsNiveis->eof() && (!$obErro->ocorreu()) ) {
                        $inValor = $rsValores->getCampo('valor');
                        $inValor = ($inValor) ? $inValor : '0';
                        if( $this->obRNivel->getCodNivel() == $rsNiveis->getCampo('cod_nivel') )
                            $inValor = ++$inUltValor;

                        $this->obTOrgaoNivel->setDado("cod_orgao"       , $this->getCodOrgao() );
                        $this->obTOrgaoNivel->setDado("cod_organograma" , $this->obROrganograma->getCodOrganograma() );
                        $this->obTOrgaoNivel->setDado("cod_nivel"       , $rsNiveis->getCampo('cod_nivel') );
                        $this->obTOrgaoNivel->setDado("valor"           , $inValor );
                        #$obErro = $this->obTOrgaoNivel->alteracao( $boTransacao );
                        #$this->obTOrgaoNivel->debug();
                        if( $obErro->ocorreu() )
                            break;

                        $rsNiveis->proximo();
                        $rsValores->proximo();
                    }

                    //Armazena os valores para serem preservados
                    $inCodOrgaoAnt       = $this->getCodOrgao();
                    $inCodOrgaoSupAnt    = $this->getCodOrgaoSuperior();
                    $inCodOrganogramaAnt = $this->obROrganograma->getCodOrganograma();

                    # Não tem necessidade de alterar níveis do órgão, já que
                    # no formulário de alteração, essas informações são carregadas como label.

                    //Efetua alteração em todos órgãos relacionados com o primeiro.
                    $rsOrgaosInferiores->setPrimeiroElemento();

                    while ( !$rsOrgaosInferiores->eof() ) {
                        $inCodOrganograma   = $rsOrgaosInferiores->getCampo('cod_organograma');
                        $inCodOrgao         = $rsOrgaosInferiores->getCampo('cod_orgao');

                        $this->obROrganograma->setCodOrganograma( $inCodOrganograma );
                        $this->setCodOrgao                      ( $inCodOrgao );
                        $this->setCodOrgaoSuperior              ( $arOrgaosSuperiores["$inCodOrganograma-$inCodOrgao"] );
                        $this->obROrganograma->listarNiveis     ( $rsNiveis, 'cod_nivel', $boTransacao );
                        $this->listarValoresSuperior            ( $rsValores, 'cod_nivel', $boTransacao );
                        $this->obRNivel->setCodNivel( count($this->retornaNivelOrgao($boTransacao)) );
                        $inUltValor = $this->recuperaUltimoValor( $boTransacao );

                        //Efetua alteração do órgão selecionado.
                        while ( !$rsNiveis->eof() && (!$obErro->ocorreu()) ) {
                            $inValor = $rsValores->getCampo('valor');
                            $inValor = ($inValor) ? $inValor : '0';

                            if( $this->obRNivel->getCodNivel() == $rsNiveis->getCampo('cod_nivel') )
                                $inValor = ++$inUltValor;

                            $this->obTOrgaoNivel->setDado("cod_orgao"       , $this->getCodOrgao() );
                            $this->obTOrgaoNivel->setDado("cod_organograma" , $this->obROrganograma->getCodOrganograma() );
                            $this->obTOrgaoNivel->setDado("cod_nivel"       , $rsNiveis->getCampo('cod_nivel') );
                            $this->obTOrgaoNivel->setDado("valor"           , $inValor );
                            #$obErro = $this->obTOrgaoNivel->alteracao( $boTransacao );

                            if( $obErro->ocorreu() )
                                break;

                            $rsNiveis ->proximo();
                            $rsValores->proximo();
                        }
                        $rsOrgaosInferiores->proximo();
                    }

                    //Retorna com os valores originais
                    $this->setCodOrgao                      ( $inCodOrgaoAnt );
                    $this->setCodOrgaoSuperior              ( $inCodOrgaoSupAnt );
                    $this->obROrganograma->setCodOrganograma( $inCodOrganogramaAnt );
                }
            }
        }
        */

        // excluindo cgm do orgao, para incluir posteriormente.
        if (!$obErro->ocorreu()) {
            $stComplemento = $this->obTOrgaoCgm->getCampoCod();
            $this->obTOrgaoCgm->setCampoCod('cod_orgao');
            $this->obTOrgaoCgm->setDado("cod_orgao"       , $this->getCodOrgao() );
            $obErro = $this->obTOrgaoCgm->exclusao ( $boTransacao );
            $this->obTOrgaoCgm->setCampoCod( $stComplemento );
        }

        if (!$obErro->ocorreu()) {
            if ($this->obRCgmPJ->getNumCGM()) {
                $stComplemento = $this->obTOrgaoCgm->getCampoCod();
                $this->obTOrgaoCgm->setCampoCod('cod_orgao');
                //$this->obTOrgaoCgm->setDado("cod_organograma" , $this->obROrganograma->getCodOrganograma() );
                $this->obTOrgaoCgm->setDado("cod_orgao"       , $this->getCodOrgao() );
                $this->obTOrgaoCgm->setDado("numcgm"          , $this->obRCgmPJ->getNumCGM() );
                $obErro = $this->obTOrgaoCgm->inclusao ( $boTransacao );
                $this->obTOrgaoCgm->setCampoCod( $stComplemento );
            }
        }
    }

    return $obErro;
}



/**
    * Efetua a inativação de um órgão.
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function inativar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTOrgao->setDado("cod_orgao"      , $this->getCodOrgao() );
        $this->obTOrgao->setDado("cod_organograma", $this->obROrganograma->getCodOrganograma() );
        $this->obTOrgao->setDado("inativacao"     , $this->getInativacao() );
        $this->consultar( $boTransacao );
        $this->obTOrgao->setDado("sigla_orgao"    , $this->getSigla() );
        $this->obTOrgao->setDado("criacao"        , $this->getCriacao() );
        $this->obTOrgao->setDado("cod_calendar"   , $this->obRCalendario->getCodCalendar() );
        $this->obTOrgao->setDado("num_cgm_pf"     , $this->obRCgmPF->getNumCGM() );
        $this->obTOrgao->setDado("cod_norma"      , $this->obRNorma->getCodNorma() );
        $obErro = $this->obTOrgao->alteracao( $boTransacao );

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}


    # Validação das tabelas possíveis de exclusão que utilizam cod_orgao.
    public function validarExclusao()
    {
        $arTable = array();

        # Tabelas que referenciam a coluna cod_orgao, caso algum registro seja
        # encontrado em pelo menos uma, não permite a exclusão do órgão.
        $arTable[] = 'administracao.comunicado';
        $arTable[] = 'administracao.impressora';
        $arTable[] = 'administracao.usuario';
        $arTable[] = 'estagio.estagiario_estagio';
        $arTable[] = 'estagio_3.estagiario_estagio';
        $arTable[] = 'folhapagamento.configuracao_empenho_lla_lotacao';
        $arTable[] = 'folhapagamento.configuracao_empenho_lotacao';
        $arTable[] = 'folhapagamento_3.configuracao_empenho_lla_lotacao';
        $arTable[] = 'folhapagamento_3.configuracao_empenho_lotacao';
        $arTable[] = 'frota.terceiros_historico';
        $arTable[] = 'ima.configuracao_banpara_orgao';
        $arTable[] = 'ima_3.configuracao_banpara_orgao';
        $arTable[] = 'organograma.de_para_setor';
        $arTable[] = 'patrimonio.historico_bem';
        $arTable[] = 'pessoal.contrato_pensionista_orgao';
        $arTable[] = 'pessoal.contrato_servidor_orgao';
        $arTable[] = 'pessoal_3.contrato_pensionista_orgao';
        $arTable[] = 'pessoal_3.contrato_servidor_orgao';
        $arTable[] = 'ponto.configuracao_lotacao';
        $arTable[] = 'ponto_3.configuracao_lotacao';
        $arTable[] = 'sw_andamento       ';
        $arTable[] = 'sw_andamento_padrao';
        $arTable[] = 'sw_ultimo_andamento';

        $boPermiteExclusao = true;
        $boFlagTransacao   = false;

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stSql = "";

        foreach ($arTable as $key => $table) {
            $stSql  = "  SELECT COUNT(cod_orgao) AS total";
            $stSql .= "    FROM ".$table;
            $stSql .= "   WHERE cod_orgao = ".$this->getCodOrgao();

            $this->obTOrgao->executaRecuperaSql($stSql, $rsValidaOrgao);

            if ($rsValidaOrgao->getCampo('total') > 0) {
                $boPermiteExclusao = false;
                break;
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        return $boPermiteExclusao;

    }

/**
  * Exclui dados de Organograma do banco de dados
  * @access Public
  * @param  Object $obTransacao Parâmetro Transação
  * @return Object Objeto Erro
  */
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ($this->validarExclusao()) {
        if ( !$obErro->ocorreu() ) {
            $this->listarOrgaosInferiores( $rsOrgaosInferiores, '', $boTransacao);

            if ( $rsOrgaosInferiores->getNumLinhas()>0 ) {
                $obErro->setDescricao('Este Órgão possui outros órgãos relacionados.');
            } else {
                $stComplementoChave = $this->obTOrgaoCgm->getCampoCod ();
                $this->obTOrgaoDescricao->setDado("cod_orgao", $this->getCodOrgao());
                $obErro =  $this->obTOrgaoDescricao->exclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $this->obTOrgaoCgm->setCampoCod("cod_orgao");
                    $this->obTOrgaoCgm->setDado("cod_orgao"       , $this->getCodOrgao() );
                    $obErro = $this->obTOrgaoCgm->exclusao( $boTransacao );
                    $this->obTOrgaoCgm->setCampoCod ( $stComplementoChave );

                    if ( !$obErro->ocorreu() ) {
                        $stComplementoChave = $this->obTOrgaoNivel->getComplementoChave();
                        $this->obTOrgaoNivel->setComplementoChave("cod_organograma");
                        $this->obTOrgaoNivel->setDado("cod_organograma" , $this->obROrganograma->getCodOrganograma() );
                        $this->obTOrgaoNivel->setDado("cod_orgao"       , $this->getCodOrgao() );
                        $obErro = $this->obTOrgaoNivel->exclusao( $boTransacao );
                        $this->obTOrgaoNivel->setComplementoChave( $stComplementoChave );
                        if ( !$obErro->ocorreu() ) {
                            $this->obTOrgao->setDado("cod_orgao"        , $this->getCodOrgao() );
                            $this->obTOrgao->setDado("cod_organograma"  , $this->obROrganograma->getCodOrganograma() );
                            $obErro = $this->obTOrgao->exclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                               if ( strpos($obErro->getDescricao(), 'fk_') ) {
                                   $obErro->setDescricao('Este Órgão não pode ser excluído porque esta sendo utilizado pelo sistema.');
                               }
                            }
                        }
                    }
                }
            }
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrgao );
        }
    } else {
        $obErro->setDescricao('Este Órgão não pode ser excluído porque esta sendo utilizado pelo sistema.');
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro .= " WHERE  1=1 ";

    if ($this->obROrganograma->getCodOrganograma())
        $stFiltro .= " AND  cod_organograma = ".$this->obROrganograma->getCodOrganograma();

    if ($this->inCodOrgao)
        $stFiltro .= " AND cod_orgao <> ".$this->inCodOrgao;

    $stOrder  = (!empty($stOrder)) ? $stOrder : " ORDER BY descricao ";

    $obErro = $this->obTOrgao->recuperaTodos ($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}
/**
    * Executa um recuperaTodos de acordo com a data atual classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUltimaCriacao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $stFiltro ='';
    if( $this->inCodOrgao )
        $this->obTOrgao->setDado('cod_orgao',$this->inCodOrgao);
    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    $date = date ("Y-m-d");
    $this->obTOrgao->setDado('data_atual',$date);
    $obErro = $this->obTOrgao->recuperaUltimaCriacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtivos(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    if( $this->inCodOrgao )
        $this->obTOrgao->setDado('oo.cod_orgao',$this->inCodOrgao);

    $this->obTOrgao->setDado('inativacao',true );
    $stOrder  = ($stOrder)  ? $stOrder : "    descricao ";
    $obErro = $this->obTOrgao->recuperaOrgaosAtivos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtivosCodigoComposto(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());

    $this->obTOrgao->setDado('inativacao',true );
    $this->obTOrgao->setDado('cod_orgao',$this->getCodOrgao() );
    $this->obTOrgao->setDado('descricao',$this->getDescricao() );
    $obErro = $this->obTOrgao->listarOrgaoCodigoComposto( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCodigoComposto(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    $obErro = $this->obTOrgao->listarOrgaoCodigoComposto( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Recupera todos os orgãos cadastrados ativos e inativos
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTodos(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    if( $this->inCodOrgao )
        $this->obTOrgao->setDado('cod_orgao',$this->inCodOrgao);

    $this->obTOrgao->setDado('inativacao',false );
    $stOrder  = ($stOrder)  ? $stOrder : "    descricao ";
    $obErro = $this->obTOrgao->recuperaOrgaosAtivos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function recuperaOrgaos(&$rsRecordSet, $boTransacao = "")
{
    if (trim($this->getCodOrgao()) != "") {
        $stFiltro = " AND orgao.cod_orgao = ".trim($this->getCodOrgao());
    }
    if (trim($this->getCodEstrutural())) {
        $stFiltro .= " AND orgao_nivel.cod_estrutural = '".trim($this->getCodEstrutural())."'";
    }
    if (trim($this->getVigencia()) == "") {
        $this->setVigencia(date("Y-m-d"));
    }
    if (trim($this->getDescricao())) {
        $stFiltro .= " AND descricao ILIKE '%".trim($this->getDescricao())."%'";
    }
    $this->obTOrgao->setDado("vigencia",$this->getVigencia());
    $stOrder  = " ORDER BY cod_estrutural";
    $obErro = $this->obTOrgao->recuperaOrgaos($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}


/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTOrgao->setDado( "cod_orgao"       , $this->inCodOrgao );
    $this->obTOrgao->setDado( "cod_organograma" , $this->obROrganograma->getCodOrganograma() );
    $obErro = $this->obTOrgao->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stSigla          = $rsRecordSet->getCampo("sigla_orgao");
        $this->stCriacao        = $rsRecordSet->getCampo("criacao");
        $this->stInativacao     = $rsRecordSet->getCampo("inativacao");
        $this->obRCgmPF->setNumCGM  ( $rsRecordSet->getCampo("num_cgm_pf") );
        $this->obRNorma->setCodNorma( $rsRecordSet->getCampo("cod_norma") );

        $inCodCalendar = $rsRecordSet->getCampo("cod_calendar");

        $obErro = $this->obRNorma->consultar( $boTransacao );

        if (  !$obErro->ocorreu()  ) {
            $this->obTOrgaoDescricao->setDado( "cod_orgao" , $this->inCodOrgao );
            $obErro = $this->obTOrgaoDescricao->recuperaUltimoOrgaoDescricao($rsRecordSet);
            $this->stDescricao = $rsRecordSet->getCampo("descricao");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRCgmPF->consultarCGM( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTOrgaoCgm->setDado("cod_orgao"         , $this->getCodOrgao() );
                $obErro = $this->obTOrgaoCgm->recuperaPorOrgao($rsOrgaoCgm,   $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( $rsOrgaoCgm->getNumLinhas() > 0 ) {
                        $this->obRCgmPJ->setNumCGM( $rsOrgaoCgm->getCampo("numcgm") );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obRCalendario->setCodCalendar( $inCodCalendar );
                        $obErro = $this->obRCalendario->consultar( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $stFiltro  = " WHERE cod_orgao = ".$this->inCodOrgao;
                            $this->obTOrgaoNivel->recuperaTodos($rsOrgaoNivel, $stFiltro, 'cod_nivel', $boTransacao );
                            $this->obROrganograma->setCodOrganograma( $rsOrgaoNivel->getCampo('cod_organograma') );
                            $obErro = $this->obROrganograma->consultar( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $arValores = array();
                                $obErro = $this->retornaNivelOrgao( $arNivelOrgao, $boTransacao);
                                if ( !$obErro->ocorreu() ) {
                                    $this->obRNivel->setCodNivel( count($arNivelOrgao) );
                                    array_pop($arNivelOrgao);
                                    $obErro = $this->consultarOrgaoSuperior( $arNivelOrgao,$this->inCodOrgaoSuperior , $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $obErro;
}
/**
    * Recupera os órgãos superiores ao órgão informado.
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrgaosSuperiores(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $rsRecordSet = new RecordSet;

    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    if ( $this->obRNivel->getCodNivel() ) {
         $this->obTOrgao->setDado( 'cod_nivel',  $this->obRNivel->getCodNivel()-1 );
    }

    if ($this->obROrganograma->getCodOrganograma() != "") {
        $stFiltro.=" cod_organograma=".$this->obROrganograma->getCodOrganograma()." AND";
    }

    if ($this->obRNivel->getCodNivel() !="") {
        $inNivel = $this->obRNivel->getCodNivel();
        $stFiltro.=" cod_nivel=".$inNivel." AND";
    }

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";

    $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsOrgaoNivel, $stFiltro, "cod_orgao,cod_nivel", $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $inCodNivel     = $this->obRNivel->getCodNivel();
        $arValoresOrgao = array();
        while ( !$rsOrgaoNivel->eof() ) {
            $arValoresOrgao[ $rsOrgaoNivel->getCampo('cod_orgao') ][] = $rsOrgaoNivel->getCampo('valor');
            $rsOrgaoNivel->proximo();
        }

        foreach ($arValoresOrgao as $inChave=>$arValor) {
            $boOrgao = true;
            if ($arValor[0] != '0') {
                $boOrgao = false;
            }

            if ($boOrgao) {
                $arOrgaos[] = $inChave;
            }
        }

        $stFiltro="";

        if ( count($arOrgaos) ) {
            $this->obTOrgao->recuperaOrgaoSuperiorNivel( $rsRecordSet, $stFiltro, "", $boTransacao );
        }
    }

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente OrgaoNivel
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrgaosInferiores(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $rsRecordSet = new RecordSet;
    $obErro = $this->retornaNivelOrgao($arNivel, $boTransacao );

    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    if( $this->obROrganograma->getCodOrganograma() )
        $this->obTOrgao->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    if ( $this->obRNivel->getCodNivel() ) {
         $this->obTOrgao->setDado( 'cod_nivel',  $this->obRNivel->getCodNivel() );
    }
    $obErro = $this->retornaValoresOrgao( $arNiveis, $boTransacao );
    if ( $obErro->ocorreu() ) {
        return $obErro;
    }
    //Lista todos os niveis de um determinado organograma
    foreach ($arNiveis as $inChave=>$arValor) {
        if ($inChave != $this->inCodOrgao) {
            $inOrgao = $inChave;
            for ($inCount=0; $inCount<count($arNivel); $inCount++) {
                //Caso alguma posicao do orgao nao seja igual, nao empilha o orgao no array
                //echo $arValor[$inCount]." != ".$arNivel[$inCount]."<br>";
                if ($arValor[$inCount] != $arNivel[$inCount]) {
                    $inOrgao = 0;
                    break;
                }
            }
            if ($inOrgao) {
                $arOrgaos[] = $inOrgao;
            }
        }
    }
    if ( count($arOrgaos) ) {


        $this->obTOrgao->setDado('cod_orgao',implode(',',$arOrgaos));

        $obErro = $this->obTOrgao->recuperaOrgaoInferior( $rsRecordSet, '', "descricao", $boTransacao );


    }

    return $obErro;
}
/**
    *
    * @access Public
    * @param  Object  $boTransacao Parâmetro Transação
    * @return Array   Retorna um array contendo todos elementos do órgão informado
*/
function recuperaUltimoValor(&$inValor, $boTransacao = "")
{
    $inCodOrgaoTmp = $this->getCodOrgao();

    if ($this->getCodOrgaoSuperior()) {
        $this->setCodOrgao( $this->getCodOrgaoSuperior() );
    }

    $obErro = $this->retornaNivelOrgao( $arNiveis, $boTransacao );

    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    $obErro = $this->obROrganograma->ListarNiveis($rsNiveis, "", $boTransacao);

    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    if ($this->obRNivel->getCodNivel() == (count($arNiveis)+1)) {
        $inFinal = count($arNiveis);
    } else {
        $inFinal = count($arNiveis) + ($this->obRNivel->getCodNivel() - (count($arNiveis)+1));
    }

    for ($inCount = 0; $inCount < $inFinal; $inCount++) {
        $stMascara = $rsNiveis->getCampo ("mascaracodigo");
        $arNiveis[$inCount] = str_pad ($arNiveis[$inCount], strlen ($stMascara), "0", STR_PAD_LEFT);
        $rsNiveis->proximo();
    }

    $stNiveis = implode('.', $arNiveis);

    $this->obROrganograma->obRNivel->setCodNivel (count($arNiveis));
    $obErro = $this->obROrganograma->ConsultarNivel($boTransacao);

    if ( $obErro->ocorreu() ) {
        return $obErro;
    }

    $stFiltro = " WHERE 1=1 ";

    if ($this->obROrganograma->getCodOrganograma()) {
        $stFiltro .= " AND  cod_organograma = ".$this->obROrganograma->getCodOrganograma();
    }

    if ($stNiveis) {
        $stFiltro .= " AND orgao like '".$stNiveis.".%' ";
    }

    $stFiltro .= " AND  nivel = ".$this->obRNivel->getCodNivel();

    $obErro = $this->obVOrgaoNivel->recuperaTodos( $rsOrgao, $stFiltro, '', $boTransacao );

    if ( !$obErro->ocorreu() ) {
        while ( !$rsOrgao->eof() ) {
            $stValor = $rsOrgao->getCampo('orgao');
            $arValor = explode(".",$stValor);
            $inValor = $arValor[ $this->obRNivel->getCodNivel()-1 ];
            $rsOrgao->proximo();
        }
    }

    $this->setCodOrgao( $inCodOrgaoTmp );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object  $boTransacao Parâmetro Transação
    * @return Array   Retorna último primeiro nível cadastrado
*/
function recuperaUltimoValorNivelPai(&$inUltimoValorNivelPai, $boTransacao = "")
{
    $stFiltro  = " WHERE cod_organograma = ".$this->obROrganograma->getCodOrganograma();
    $stFiltro .= " AND   cod_nivel       = 1";
    $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsOrgaoNivel, $stFiltro, "TO_NUMBER( valor,'999999999999999999999999999999' ) DESC", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inUltimoValorNivelPai = $rsOrgaoNivel->getCampo("valor");
    }

    return $obErro;
}

/**
    * Retorna o Nível de um determinado órgão, relacionado a um organograma
    * @access Public
    * @param  Object  $boTransacao Parâmetro Transação
    * @return Array   Retorna um array contendo todos elementos do órgão informado
*/
function retornaNivelOrgao(&$arElementos, $boTransacao = "")
{
    $stFiltro  = " WHERE  1=1 ";

    if ($this->obROrganograma->getCodOrganograma()) {
        $stFiltro .= " AND  cod_organograma = ".$this->obROrganograma->getCodOrganograma();
    }

    if ($this->inCodOrgao) {
        $stFiltro .= " AND cod_orgao = ".$this->inCodOrgao;
    }

    $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsOrgaoNivel, $stFiltro, "cod_orgao ASC ,cod_nivel DESC", $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $arElementos = array();
        while ( !$rsOrgaoNivel->eof() ) {
            if ( $rsOrgaoNivel->getCampo('valor') || (count($arElementos)>0) ) {
                array_unshift( $arElementos , $rsOrgaoNivel->getCampo('valor') );
            }
            $rsOrgaoNivel->proximo();
        }
    }

    return $obErro;
}
/**
    * Retorna uma matriz contendo todos os orgaos de um determinado organograma. $arMatriz[cod_orgao][valores]
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Array  Retorna uma matriz $arMatriz[cod_orgao][valores]
*/
function retornaValoresOrgao(&$arValoresOrgao, $boTransacao = "")
{
    $rsOrgaoNivel = new RecordSet;
    $stFiltro  = " WHERE cod_organograma = ".$this->obROrganograma->getCodOrganograma();
    $obErro = $this->obTOrgaoNivel->recuperaTodos($rsOrgaoNivel, $stFiltro, 'cod_organograma,cod_orgao,cod_nivel', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsOrgaoNivel->eof() ) {
            $arValoresOrgao[ $rsOrgaoNivel->getCampo('cod_orgao') ][] = $rsOrgaoNivel->getCampo('valor');
            $rsOrgaoNivel->proximo();
        }
    }

    return $obErro;
}
/**
    * Retorna os Valores do Órgão Superior informado. Caso não seja informado o Orgao Superior, pega o próximo
    * órgão de primeiro nível
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarValoresSuperior(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->inCodOrgaoSuperior )
        $stFiltro .= " cod_orgao = " . $this->inCodOrgaoSuperior . " AND ";
    if( $this->obROrganograma->getCodOrganograma() )
        $stFiltro .= " cod_organograma = " . $this->obROrganograma->getCodOrganograma() . " AND ";

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";
    $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Retorna os Valores do Órgão Superior informado. Caso não seja informado o Orgao Superior, pega o próximo
    * órgão de primeiro nível
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrgaoReduzido(&$rsRecordSet, $stFiltro="",$stOrder = "", $boTransacao = "")
{
    if( $this->inCodOrgao )
        $stFiltro = " AND ovw.cod_orgao = '". $this->inCodOrgao ."'\n";
    if( $this->inCodOrgaoReduzido )
        $stFiltro = " AND ovw.orgao_reduzido = '". $this->inCodOrgaoReduzido ."'\n";
    if( $this->inCodOrgaoEstruturado )
        $stFiltro = " AND ovw.orgao = '". $this->inCodOrgaoEstruturado ."'\n";
    if( $this->stDescricao )
        $stFiltro = " AND orgao.descricao ilike  '%".$this->stDescricao."%'\n";
    $date = date ("Y-m-d");
    $this->obTOrgao->setDado('data_atual',$date);
    $obErro = $this->obTOrgao->recuperaOrgaoReduzido( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Identifica qual é o órgão superior em relação aos valores informados.
    * @access Public
    * @param  Array  $arValores Vetor com a chave completa que indica o Órgão Superior
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarOrgaoSuperior($arValores, &$inCodOrgaoSuperior, $boTransacao = "")
{
    if ( count($arValores)>0 ) {
        $stFiltro  = " WHERE cod_orgao = ".$this->inCodOrgao;
        $stFiltro .= " AND cod_organograma = ".$this->obROrganograma->getCodOrganograma();
        $obErro = $this->obTOrgaoNivel->recuperaTodos($rsOrgaoNivel, $stFiltro, 'cod_nivel', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            while ( !$rsOrgaoNivel->eof() ) {
                if ( $rsOrgaoNivel->getCorrente()>count($arValores) ) {
                    $arValores[] = '0';
                }
                $rsOrgaoNivel->proximo();
            }
            $obErro = $this->retornaValoresOrgao ( $arValoresOrgao, $boTransacao);
            if ( !$obErro->ocorreu() ) {
                foreach ($arValoresOrgao as $inChave=>$arValor) {
                    if ($arValor==$arValores) {
                        $inCodOrgaoSuperior = $inChave;
                    }
                }
            }
        }
    }

    return $obErro;
}

/**
    * Monta Array para a visualizacao de organograma
    * @access Public
    * @return Array dados do organograma
*/
function listaVisualizacaoOrganograma(&$arNiveisOrganograma)
{
    $this->obTOrgao->setDado('cod_organograma', $this->obROrganograma->getCodOrganograma());
    $obErro = $this->obTOrgao->listarOrgaoCodigoComposto($rsNiveis,"", "orgao,cod_orgao" );
    if ( $rsNiveis->getNumLinhas () < 0 ) {
        $obErro->setDescricao ("Não é possível visualizar organograma pois não existe órgão cadastrado");

        return $obErro;
    }
    if ( !$obErro->ocorreu () ) {
        $inCount = 0;
        $inCount2 = 0;
        while (!$rsNiveis->eof ()) {
            if ($rsNiveis->getCampo ("nivel") != 0) {
                $arNiveisOrganograma[$inCount][$inCount2]["cod_orgao"]       = $rsNiveis->getCampo ("cod_orgao");
                $arNiveisOrganograma[$inCount][$inCount2]["cod_organograma"] = $rsNiveis->getCampo ("cod_organograma");
                $arNiveisOrganograma[$inCount][$inCount2]["num_cgm_pf"]      = $rsNiveis->getCampo ("num_cgm_pf");
                $arNiveisOrganograma[$inCount][$inCount2]["cod_calendar"]    = $rsNiveis->getCampo ("cod_calendar");
                $arNiveisOrganograma[$inCount][$inCount2]["cod_norma"]       = $rsNiveis->getCampo ("cod_norma");
                $arNiveisOrganograma[$inCount][$inCount2]["descricao"]       = $rsNiveis->getCampo ("descricao");
                $arNiveisOrganograma[$inCount][$inCount2]["criacao"]         = $rsNiveis->getCampo ("criacao");
                $arNiveisOrganograma[$inCount][$inCount2]["inativacao"]      = $rsNiveis->getCampo ("inativacao");
                $arNiveisOrganograma[$inCount][$inCount2]["orgao"]           = $rsNiveis->getCampo ("orgao");
                $arNiveisOrganograma[$inCount][$inCount2]["orgao_reduzido"]  = $rsNiveis->getCampo ("orgao_reduzido");
                $arNiveisOrganograma[$inCount][$inCount2]["nivel"]           = $rsNiveis->getCampo ("nivel");

                if ( $rsNiveis->getCampo ("situacao") == "inativo" ) {
                    $arNiveisOrganograma[$inCount][$inCount2]["situacao"]        = "(inativo)";
                } else {
                    $arNiveisOrganograma[$inCount][$inCount2]["situacao"]        = "";
                }
                $inCount2++;
                $rsNiveis->proximo ();
                if ( $rsNiveis->getCampo("nivel") == 1 ) {
                    $inCount++;
                    $inCount2 = 0;
                }
            } else {
                $rsNiveis->proximo();
            }
        }
    }

    return $obErro;
}
/**
    * Monta Array para a visualizacao de organograma na POP UP
    * @access Public
    * @return Array dados do organograma
*/

function listaVisualizacaoOrganogramaPopUp(&$arNiveisOrganograma)
{
    $this->obTOrgaoNivelPopUp->setDado('cod_orgao',$this->getCodOrgao());
    $this->obTOrgaoNivelPopUp->setDado('cod_organograma',$this->obROrganograma->getCodOrganograma());
    $obErro = $this->obTOrgaoNivelPopUp->recuperaTodos($rsNiveis,$stFiltro,$stOrder,$boTransacao);
    if ( $rsNiveis->getNumLinhas () < 0 ) {
        $obErro->setDescricao ("Não é possível visualizar organograma pois não existe órgão cadastrado");

        return $obErro;
    }
    if ( !$obErro->ocorreu () ) {
        $inCount = 0;
        $inCount2 = 0;
        while (!$rsNiveis->eof ()) {
            $arNiveisOrganograma[$inCount][$inCount2]["cod_orgao"]       = $rsNiveis->getCampo ("cod_orgao");
            $arNiveisOrganograma[$inCount][$inCount2]["descricao"]       = $rsNiveis->getCampo ("descricao");
            $arNiveisOrganograma[$inCount][$inCount2]["orgao"]           = $rsNiveis->getCampo ("orgao");
            $arNiveisOrganograma[$inCount][$inCount2]["orgao_reduzido"]  = $rsNiveis->getCampo ("orgao_reduzido");
            $arNiveisOrganograma[$inCount][$inCount2]["nivel"]           = $rsNiveis->getCampo ("nivel");
            $inCount2++;
            $rsNiveis->proximo ();
            if ( $rsNiveis->getCampo("nivel") == 1 ) {
                $inCount++;
                $inCount2 = 0;
            }
        }
    }

    return $obErro;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    if ($this->getCodOrgao()) {
       $this->obTOrgao->setDado("cod_orgao", $this->getCodOrgao() );
    }
    if ($this->obROrganograma->getCodOrganograma()) {
        $this->obTOrgao->setDado("cod_organograma", $this->obROrganograma->getCodOrganograma() );
    }
    $this->obTOrgao->setDado("inativacao", true );
    $obErro = $this->obTOrgao->recuperaRelacionamento( $rsRecordSet, '','oo.cod_orgao' );

    return $obErro;
}

}

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
    * Classe de negócio Funcao
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: RFuncao.class.php 66309 2016-08-05 21:32:46Z michel $

    Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RTipoPrimitivo.class.php"             );
include_once ( CAM_GA_ADM_NEGOCIO."RVariavel.class.php"                  );
include_once ( CAM_GA_ADM_NEGOCIO."RBiblioteca.class.php"                );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncaoExterna.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCorpoFuncaoExterna.class.php" );

class RFuncao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodFuncao;
/**
    * @access Private
    * @var String
*/
var $stNomeFuncao;
/**
    * @access Private
    * @var String
*/
var $stComentario;
/**
    * @access Private
    * @var String
*/
var $stTipoFuncao;
/**
    * @access Private
    * @var String
*/
var $stCorpoPL;
/**
    * @access Private
    * @var String
*/
var $stCorpoLN;
/**
    * @access Private
    * @var Object
*/
var $obTFuncao;
/**
    * @access Private
    * @var Object
*/
var $obTFuncaoExterna;
/**
    * @access Private
    * @var Object
*/
var $obTCorpoFuncaoExterna;
/**
    * @access Private
    * @var Object
*/
var $obRBiblioteca;
/**
    * @access Private
    * @var Object
*/
var $obRTipoPrimitivo;
/**
    * @access Private
    * @var Object
*/
var $obRVariavel;
/**
    * @access Private
    * @var Object
*/
var $obUltimaVariavel;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Array
*/
var $arVariaveis;
/**
    * @access Private
    * @var Object
*/
var $rsCorpoLN;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodFuncao($valor) { $this->inCodFuncao           = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomeFuncao($valor) { $this->stNomeFuncao          = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setComentario($valor) { $this->stComentario          = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setTipoFuncao($valor) { $this->stTipoFuncao          = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setCorpoPL($valor) { $this->stCorpoPL             = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setCorpoLN($valor) { $this->stCorpoLN             = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTFuncao($valor) { $this->obTFuncao             = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTFuncaoExterna($valor) { $this->obTFuncaoExterna      = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTCorpoFuncaoExterna($valor) { $this->obTCorpoFuncaoExterna = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRBiblioteca($valor) { $this->obRBiblioteca         = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setCodBiblioteca($valor) { $this->inCodBiblioteca = $valor;}
/**
    * @access Public
    * @param Object $Valor
*/
function setCodModulo($valor) { $this->inCodModulo = $valor;}
/**
    * @access Public
    * @param Object $Valor
*/
function setRTipoPrimitivo($valor) { $this->obRTipoPrimitivo      = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRVariavel($valor) { $this->obRVariavel           = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setUltimaVariavel($valor) { $this->obUltimaVariavel    = $valor; }
/**
     * @access Public
     * @param Array $valor
*/
function setVariaveis($valor) { $this->arVariaveis         = $valor;  }
/**
     * @access Public
     * @param Object $valor
*/
function setRSCorpoLN($valor) { $this->rsCorpoLN           = $valor;  }

/**
    * @access Public
    * @return Integer
*/
function getCodFuncao() { return $this->inCodFuncao           ; }
/**
    * @access Public
    * @return String
*/
function getNomeFuncao() { return $this->stNomeFuncao          ; }
/**
    * @access Public
    * @return String
*/
function getCodBiblioteca() { return $this->inCodBiblioteca;}
/**
    * @access Public
    * @return String
*/
function getCodModulo() { return $this->inCodModulo;}
/**
    * @access Public
    * @return String
*/
function getComentario() { return $this->stComentario          ; }
/**
    * @access Public
    * @return String
*/
function getTipoFuncao() { return $this->stTipoFuncao          ; }
/**
    * @access Public
    * @return String
*/
function getCorpoPL() { return $this->stCorpoPL             ; }
/**
    * @access Public
    * @return String
*/
function getCorpoLN() { return $this->stCorpoLN             ; }
/**
    * @access Public
    * @return Object
*/
function getTFuncao() { return $this->obTFuncao             ; }
/**
    * @access Public
    * @return Object
*/
function getTFuncaoExterna() { return $this->obTFuncaoExterna      ; }
/**
    * @access Public
    * @return Object
*/
function getTCorpoFuncaoExterna() { return $this->obTCorpoFuncaoExterna ; }
/**
    * @access Public
    * @return Object
*/
function getRBiblioteca() { return $this->obRBiblioteca         ; }
/**
    * @access Public
    * @return Object
*/
function getRTipoPrimitivo() { return $this->obRTipoPrimitivo      ; }
/**
    * @access Public
    * @return Object
*/
function getRVariavel() { return $this->obRVariavel           ; }
/**
     * @access Public
     * @return Object
*/
function getUltimaVariavel() { return $this->obUltimaVariavel     ; }
/**
     * @access Public
     * @return Array
*/
function getVariaveis() { return $this->arVariaveis          ; }
/**
     * @access Public
     * @return Object
*/
function getRSCorpoLN() { return $this->rsCorpoLN            ; }

/**
     * Método construtor
     * @access Private
*/
function RFuncao()
{
    $this->setTFuncao             ( new TAdministracaoFuncao             );
    $this->setTFuncaoExterna      ( new TAdministracaoFuncaoExterna      );
    $this->setTCorpoFuncaoExterna ( new TAdministracaoCorpoFuncaoExterna );
    $this->setRBiblioteca         ( new RBIblioteca( new RModulo ) );
    $this->setRTipoPrimitivo      ( new RTipoPrimitivo        );
    $this->setRVariavel           ( new RVariavel             );
    $this->stTipoFuncao           = null;
    $this->obTransacao            = new Transacao;
}
/**
    * Instancia um novo objeto do tipo Variavel
    * @access Public
*/
function addVariavel()
{
    $this->setUltimaVariavel( new RVariavel );
}
/**
    * Instancia um novo objeto do tipo Parametro
    * @access Public
*/
function addParametro()
{
    $obParametro = new RVariavel;
    $obParametro->setParametro( true );
    $this->setUltimaVariavel( $obParametro );
}
/**
    * Adiciona o objeto do tipo cargo ao array de Variaveis
    * @access Public
*/
function commitVariavel()
{
    $arVariavel   = $this->getVariaveis();
    $arVariavel[] = $this->getUltimaVariavel();
    $this->setVariaveis( $arVariavel );
}
/**
    * Executa um recuperaTodos na classe Persistente de Função
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->obRTipoPrimitivo->stNomeTipo )
        $stFiltro .= " AND nom_tipo = '".$this->obRTipoPrimitivo->stNomeTipo."' ";
    if( $this->stNomeFuncao )
        $stFiltro .= " AND upper( nom_funcao) like upper( '".$this->stNomeFuncao."%' ) ";
    if( $this->inCodBiblioteca )
        $stFiltro .= " AND fe.cod_biblioteca =  ".$this->inCodBiblioteca."";
    if( $this->inCodBiblioteca and $this->inCodModulo)
        $stFiltro .= " AND fe.cod_biblioteca =  ".$this->inCodBiblioteca."". "AND fe.cod_modulo = ".$this->inCodModulo."";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY nom_funcao ";

    if ($this->stTipoFuncao != null) {
        if ( strtolower(trim($this->stTipoFuncao)) == "externa" ) {
            $obErro = $this->obTFuncaoExterna->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        } elseif ( strtolower(trim($this->stTipoFuncao)) == "interna" ) {
            $obErro = $this->obTFuncao->recuperaFuncaoInterna( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        }
    } else {
        $obErro = $this->obTFuncao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente de Função
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPorModuloBiblioteca(&$rsRecordSet,$stFiltro="", $stOrder = "", $boTransacao = "")
{
    if( $this->obRTipoPrimitivo->stNomeTipo )
        $stFiltro .= " AND nom_tipo = '".$this->obRTipoPrimitivo->stNomeTipo."' ";
    if( $this->stNomeFuncao )
        $stFiltro .= " AND  nom_funcao ilike  '%".$this->stNomeFuncao."%'  ";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY nom_funcao ";
    if ($this->stTipoFuncao != null) {
        if ( strtolower(trim($this->stTipoFuncao)) == "externa" ) {
            $obErro = $this->obTFuncaoExterna->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        } elseif ( strtolower(trim($this->stTipoFuncao)) == "interna" ) {
            $obErro = $this->obTFuncao->recuperaFuncaoInterna( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        } else {
            $obErro = $this->obTFuncao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        }
    } else {
        $obErro = $this->obTFuncao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente de Corpo função externa
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCorpoFuncao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro  = " WHERE ";
    $stFiltro .= "     cod_funcao = ".$this->inCodFuncao." AND ";
    $stFiltro .= "     cod_modulo = ".$this->obRBiblioteca->roRModulo->getCodModulo()." AND ";
    $stFiltro .= "     cod_biblioteca = ".$this->obRBiblioteca->getCodigoBiblioteca()." ";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY cod_funcao,cod_linha ";
    $obErro = $this->obTCorpoFuncaoExterna->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Tipo Primitivo
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTFuncao->setDado( "cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
    $this->obTFuncao->setDado( "cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
    $this->obTFuncao->setDado( "cod_funcao" , $this->getCodFuncao() );
    $obErro = $this->obTFuncao->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNomeFuncao  ( $rsRecordSet->getCampo("nom_funcao") );
        $this->obTFuncaoExterna->setDado( "cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
        $this->obTFuncaoExterna->setDado( "cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
        $this->obTFuncaoExterna->setDado( "cod_funcao" , $this->getCodFuncao() );
        $this->obTFuncaoExterna->setDado( "cod_funcao" , $this->getCodFuncao() );
        $inCodTipoRetorno = $rsRecordSet->getCampo("cod_tipo_retorno");
        $obErro = $this->obTFuncaoExterna->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setComentario  ( $rsRecordSet->getCampo("comentario") );
            $this->setCorpoPL     ( $rsRecordSet->getCampo("corpo_pl") );
            $this->obRTipoPrimitivo->setCodTipo( $inCodTipoRetorno );
            $obErro = $this->obRTipoPrimitivo->consultar( $boTransacao );
        }
    }

    return $obErro;
}
/**
    * Verifica a integridade do nome da função
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaIntegridade($boTransacao = "")
{
    $obErro = new Erro;
    $stCampoCod  = $this->obTFuncao->getCampoCod();
    $stCompChave = $this->obTFuncao->getComplementoChave();
    $this->obTFuncao->setCampoCod        ( "nom_funcao" );
    $this->obTFuncao->setDado            ( "nom_funcao", $this->getNomeFuncao() );
    $this->obTFuncao->recuperaPorChave   ( $rsRecordSet, $boTransacao );
    $this->obTFuncao->setCampoCod        ( $stCampoCod );
    $this->obTFuncao->setComplementoChave( $stCompChave );
    if ( !$rsRecordSet->eof() ) {
        $obErro->setDescricao( " Função ".$this->getNomeFuncao()." já existente. " );
    }

    return $obErro;
}
/**
    * Retorna a palavra reservada convertida da linguagem natural p/ PLpgSQL
    * @access Public
    * @param  String $stPalavraReservada
    * @return String Palavra reservada da linguagem PLpgSQL
*/
function convertePalavraReservada($stPalavraReservada)
{
    switch ( trim(strtoupper($stPalavraReservada)) ) {
        case "INTEIRO"     : $stLinguagem = "INTEGER";    break;
        case "DATA"        : $stLinguagem = "DATE";       break;
        case "TEXTO"       : $stLinguagem = "VARCHAR";    break;
        case "BOOLEANO"    : $stLinguagem = "BOOLEAN";    break;
        case "NUMERICO"    : $stLinguagem = "NUMERIC";    break;
        case "E"           : $stLinguagem = "AND";        break;
        case "OU"          : $stLinguagem = "OR";         break;
        case "VERDADEIRO"  : $stLinguagem = "TRUE";       break;
        case "FALSO"       : $stLinguagem = "FALSE";      break;
        case "RETORNA"     : $stLinguagem = "RETURN";     break;
        case "SE"          : $stLinguagem = "IF";         break;
        case "ENTAO"       : $stLinguagem = "THEN";       break;
        case "SENAO"       : $stLinguagem = "ELSE";       break;
        case "FIMSE"       : $stLinguagem = "END IF;";    break;
        case "ENQUANTO"    : $stLinguagem = "WHILE";      break;
        case "FACA"        : $stLinguagem = "LOOP";       break;
        case "FIMENQUANTO" : $stLinguagem = "END LOOP;";  break;
        case "FIMFUNCAO"   : $stLinguagem = "END;".chr(13).chr(10)." \' LANGUAGE \'plpgsql\'; ";   break;
        case "FUNCAO"      : $stLinguagem = "FUNCTION ";
        default            : $stLinguagem =  null;
    }

    return $stLinguagem;
}
/**
    * Salva dados de função externa e corpo da função no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( ($this->obRTipoPrimitivo->getNomeTipo()) && !($this->obRTipoPrimitivo->getCodTipo()) ) {
            $this->obRTipoPrimitivo->listar( $rsTipo, '', $boTransacao);
            $this->obRTipoPrimitivo->setCodTipo( $rsTipo->getCampo('cod_tipo') );
        }
        //SETA OS DADOS DA FUNCAO
        $this->obTFuncao->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
        $this->obTFuncao->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
        $this->obTFuncao->setDado("nom_funcao"      , $this->getNomeFuncao() );
        $this->obTFuncao->setDado("cod_tipo_retorno", $this->obRTipoPrimitivo->getCodTipo() );
        //VERIFICA QUAL AÇÃO ESTA SENDO EXECUTADA
        if ( $this->getCodFuncao() ) {
            $this->obTFuncao->setDado("cod_funcao", $this->getCodFuncao() );
            $obErro = $this->obTFuncao->alteracao( $boTransacao );
        } else {
            $obErro = $this->verificaIntegridade( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTFuncao->proximoCod( $inCodFuncao , $boTransacao );
                $this->setCodFuncao( $inCodFuncao );
                $this->obTFuncao->setDado("cod_funcao", $this->getCodFuncao()  );
                $obErro = $this->obTFuncao->inclusao( $boTransacao );
            }
        }
        if ( !$obErro->ocorreu() ) {
            //INCLUSAO OU ALTERACAO DO CORPO DA FUNCAO
            $this->obTFuncaoExterna->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
            $this->obTFuncaoExterna->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
            $this->obTFuncaoExterna->setDado("cod_funcao", $this->getCodFuncao()  );
            $this->obTFuncaoExterna->setDado("cod_tipo", $this->obRTipoPrimitivo->getCodTipo() );
            $obErro = $this->obTFuncaoExterna->recuperaPorChave( $rsFuncaoExterna, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTFuncaoExterna->setDado("comentario", $this->getComentario() );
                $this->setCorpoPL( str_replace("\\\'","\'",$this->getCorpoPL()) );
                $this->setCorpoPL( str_replace("\'","'",$this->getCorpoPL()) );
                $this->setCorpoPL( str_replace("<div align=\"left\"><pre>","",$this->getCorpoPL()) );
                $this->setCorpoPL( str_replace("</pre></div>","",$this->getCorpoPL()) );
                $this->setCorpoPL( str_replace("<br>",chr(13).chr(10),$this->getCorpoPL()) );
                $this->setCorpoPL( SistemaLegado::unhtmlentities($this->getCorpoPL()) );

                $this->obTFuncaoExterna->setDado("corpo_pl"  , $this->getCorpoPL()    );
                //VERIFICA A ACAO
                if( $rsFuncaoExterna->eof() )
                    $obErro = $this->obTFuncaoExterna->inclusao( $boTransacao );
                else
                    $obErro = $this->obTFuncaoExterna->alteracao( $boTransacao );
            }
            $this->obRVariavel->setCodModulo( $this->obRBiblioteca->roRModulo->getCodModulo() );
            $this->obRVariavel->setCodBiblioteca( $this->obRBiblioteca->getCodigoBiblioteca() );
            $this->obRVariavel->setCodFuncao( $this->getCodFuncao() );
            $this->obRVariavel->setParametro( true );
            $this->obRVariavel->excluir( $boTransacao );
            foreach ( $this->getVariaveis() as $obVariavel ) {
                $obVariavel->setCodModulo( $this->obRBiblioteca->roRModulo->getCodModulo() );
                $obVariavel->setCodBiblioteca( $this->obRBiblioteca->getCodigoBiblioteca() );
                $obVariavel->setCodFuncao( $this->getCodFuncao() );
                $obErro = $obVariavel->salvar( $boTransacao );

                if( $obErro->ocorreu() )
                    break;
            }

            if ( !$obErro->ocorreu() ) {
                $rsCorpo = $this->getRSCorpoLN();

                $stCampoCod  = $this->obTCorpoFuncaoExterna->getCampoCod();
                $stCompChave = $this->obTCorpoFuncaoExterna->getComplementoChave();
                $this->obTCorpoFuncaoExterna->setDado("cod_funcao", $this->getCodFuncao() );
                $this->obTCorpoFuncaoExterna->setCampoCod("cod_funcao");
                $this->obTCorpoFuncaoExterna->setComplementoChave("cod_modulo,cod_biblioteca");
                $this->obTCorpoFuncaoExterna->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
                $this->obTCorpoFuncaoExterna->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
                $obErro = $this->obTCorpoFuncaoExterna->exclusao( $boTransacao );
                $this->obTCorpoFuncaoExterna->setCampoCod        ( $stCampoCod );
                $this->obTCorpoFuncaoExterna->setComplementoChave( $stCompChave );

                while ( !$rsCorpo->eof() && !$obErro->ocorreu() ) {
                    if(is_array($rsCorpo->getObjeto()) && count($rsCorpo->getObjeto())>1) {
                        $inNivel    = $rsCorpo->getCampo("Nivel");
                        $stConteudo = $rsCorpo->getCampo("Conteudo");
                        $stConteudo = SistemaLegado::unhtmlentities($stConteudo);
                        $this->obTCorpoFuncaoExterna->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
                        $this->obTCorpoFuncaoExterna->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
                        $this->obTCorpoFuncaoExterna->setDado("cod_funcao", $this->getCodFuncao() );
                        $this->obTCorpoFuncaoExterna->proximoCod( $inCodLinha , $boTransacao );
                        $this->obTCorpoFuncaoExterna->setDado("cod_linha", $inCodLinha );
                        $this->obTCorpoFuncaoExterna->setDado("nivel"    , $inNivel    );
                        $this->obTCorpoFuncaoExterna->setDado("linha"    , $stConteudo );
                        $obErro = $this->obTCorpoFuncaoExterna->inclusao( $boTransacao );

                        if( $obErro->ocorreu() )
                            break;
                    }

                    $rsCorpo->proximo();
                }
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->executaFuncaoPL( $boTransacao );
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}
/**
    * Excecuta função PLpgSQL no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function executaFuncaoPL($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;

    $stDML  = "CREATE OR REPLACE ";
    $stDML .= $this->getCorpoPL();
    $stDML  = str_replace("\'","'",$stDML);

    $obErro = $obConexao->executaDML( $stDML, $boTransacao );

    return $obErro;
}
/**
    * Executa conversão de tipos entre LN e PL
    * @access Public
    * @param  String $stPalavra Palavra reservada em LN
    * @return String Palavra reservada em PL
*/
function converteTipo($stPalavra)
{
    switch ($stPalavra) {
        case "INTEIRO"     : $stPalavra = "INTEGER";    break;
        case "TEXTO"       : $stPalavra = "VARCHAR";    break;
        case "BOOLEANO"    : $stPalavra = "BOOLEAN";    break;
        case "NUMERICO"    : $stPalavra = "NUMERIC";    break;
        case "DATA"        : $stPalavra = "DATE";       break;
    }

    return $stPalavra;
}
/**
    * Exclui dados de Função e relacionados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $obErro = $this->obTFuncao->recuperaTabelasRelacionamentoFuncao( $rsNomeTabela , $boTransacao );
    $arrTabelas = $rsNomeTabela->arElementos;

    // Cria e preenche o array com os dados necessários para consultar.
    $arrDados['cod_funcao']     = $this->getCodFuncao();
    $arrDados['cod_modulo']     = $this->obRBiblioteca->roRModulo->getCodModulo();
    $arrDados['cod_biblioteca'] = $this->obRBiblioteca->getCodigoBiblioteca();

    // Percorre o recordSet e cria um array com os nomes das tabelas que possuem a função.
    $i = 1;
    foreach ($arrTabelas as $k=>$v) {
        foreach ($v as $coluna=>$valor) {
            if ( $coluna == "schema_origem" )
                $arrNomesTabelas[$i][$coluna] = $valor;

            if ( $coluna == "tabela_origem" )
                $arrNomesTabelas[$i][$coluna] = $valor;

        }
        $i++;
    }

    // Percorre o array e pesquisa nas tanelas se existe registro.
    foreach ($arrNomesTabelas as $valoresTabelas) {
        $schemaTabela = "";
        foreach ($valoresTabelas as $coluna=>$valor) {
                if ( $coluna == "schema_origem" )
                    $schemaTabela = $valor;

                if ( $coluna == "tabela_origem" )
                    $schemaTabela = $schemaTabela.".".$valor;
        }

        // Desconsidera as tabelas que são sempre vinculadas a função.
        if ($schemaTabela != "administracao.funcao_externa" &&
            $schemaTabela != "administracao.variavel"		&&
            $schemaTabela != "administracao.parametro")
        {
            $obErro = $this->obTFuncao->recuperaExistenciaRegistrosFuncao($rsNomeTabela , $boTransacao, $schemaTabela, $arrDados);
            $msg .= ($rsNomeTabela->getNumLinhas() > 0) ? " ".$schemaTabela."," : "";
        }
    }

    // Seta a mensagem de erro, retirando o último caracter separador.
    if (!empty($msg))
        $obErro->setDescricao("Essa função não pode ser excluída, pois existem registros em: ".substr($msg,0,strlen($msg)-1));

    if ( !$obErro->ocorreu() ) {
        $this->obRVariavel->setCodModulo(  $this->obRBiblioteca->roRModulo->getCodModulo() );
        $this->obRVariavel->setCodBiblioteca(  $this->obRBiblioteca->getCodigoBiblioteca() );
        $this->obRVariavel->setCodFuncao( $this->getCodFuncao() );
        $this->obRVariavel->setParametro( true );
        $this->obRVariavel->listar( $rsParametros, '', $boTransacao );
        $obErro = $this->obRVariavel->excluir( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCorpoFuncaoExterna->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
            $this->obTCorpoFuncaoExterna->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
            $this->obTCorpoFuncaoExterna->setDado("cod_funcao", $this->getCodFuncao() );
            $this->obTCorpoFuncaoExterna->setCampoCod("cod_funcao");
            $this->obTCorpoFuncaoExterna->setComplementoChave("");
            $obErro = $this->obTCorpoFuncaoExterna->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTFuncaoExterna->setDado("cod_funcao", $this->getCodFuncao() );
                $this->obTFuncaoExterna->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
                $this->obTFuncaoExterna->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
                $obErro = $this->obTFuncaoExterna->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTFuncao->setDado("cod_modulo", $this->obRBiblioteca->roRModulo->getCodModulo() );
                    $this->obTFuncao->setDado("cod_biblioteca", $this->obRBiblioteca->getCodigoBiblioteca() );
                    $this->obTFuncao->setDado("cod_funcao", $this->getCodFuncao() );
                    $this->obTFuncao->recuperaPorChave( $rsNomeFuncao , $boTransacao );
                    $obErro = $this->obTFuncao->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $obConexao  = new Conexao;

                        $stParametros = "( ";
                        while ( !$rsParametros->eof() ) {
                            $stParametros .= $this->converteTipo( $rsParametros->getCampo("nom_tipo") ).",";//tirar upper
                            $rsParametros->proximo();
                        }
                        $stParametros = substr($stParametros,0,strlen($stParametros)-1);
                        $stParametros .= ")";
                        $stDML  = "DROP FUNCTION " . $rsNomeFuncao->getCampo("nom_funcao");
                        $stDML .= $stParametros.";";
                        $obErro = @$obConexao->executaDML( $stDML, $boTransacao );
                    }
                    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
                }
            }
        }
    }

    return $obErro;
}

function montaCorpoFuncao($inChaveChecked='')
{
    $stQuebra = chr(13).chr(10);
    $arFuncao = Sessao::read('Funcao');
    $stLinha1  = "FUNCAO " . $arFuncao['Nome'];
    $stLinha1 .= "(";

    if ( is_array($arFuncao['Parametro']) ) {
        foreach ($arFuncao['Parametro'] as $inChave=>$stValor) {
            $stLinha1 .= "$stValor,";
            if($inChave==count($arFuncao['Parametro'])-1)
                $stLinha1 = substr($stLinha1,0,strlen($stLinha1)-1);
        }
    }
    $stLinha1 .= ") RETORNA " . $arFuncao['Retorno'];
    $stFuncao = $stLinha1.$stQuebra;

    $obExcluir = new Acao;
    $obExcluir->setAcao("excluir");

    $obFormulario = new Formulario;

    /* SERÁ IMPLEMENTADO NOVAS FUNCIONALIDADES DE NAVEGAÇÃO.
    # Início da Legenda
    $obFormulario->addLinha();
    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
    $obFormulario->ultimaLinha->ultimaCelula->addConteudo('Legenda');
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
    $obFormulario->ultimaLinha->ultimaCelula->addConteudo('[]');
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->commitLinha();
    # Fim da Legenda
    */

    $obFormulario->addLinha();

    for ($inCount=0; $inCount<=3; $inCount++) {
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
        $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
        $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
        $obFormulario->ultimaLinha->commitCelula();
    }

    $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
    $obFormulario->ultimaLinha->ultimaCelula->setColSpan(5);
    $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
    $obFormulario->ultimaLinha->ultimaCelula->addConteudo( $stLinha1 );
    $obFormulario->commitLinha();
    $obFormulario->addLinha();

    for ($inCount=0; $inCount<3; $inCount++) {
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
        $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
        $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
        $obFormulario->ultimaLinha->commitCelula();
    }

    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
    $obFormulario->ultimaLinha->ultimaCelula->setColSpan(5);
    $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
    $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "DECLARA" );
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->commitLinha();
    $stFuncao .= "DECLARA".$stQuebra;

    if ( is_array($arFuncao['Variavel']) ) {
        foreach ($arFuncao['Variavel'] as $inChave=>$stValor) {
            $obFormulario->addLinha();
            for ($inCount=0; $inCount<3; $inCount++) {
                $obFormulario->ultimaLinha->addCelula();
                $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
                $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
                $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                $obFormulario->ultimaLinha->commitCelula();
            }
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
            $obFormulario->ultimaLinha->ultimaCelula->setColSpan(5);
            $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
            $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;&nbsp;$stValor;" );
            $obFormulario->ultimaLinha->commitCelula();
            $obFormulario->commitLinha();
            $stFuncao .= "  $stValor;".$stQuebra;
        }
    }
    $obFormulario->addLinha();
    $inCountFinal = (count($arFuncao['Variavel'])>0) ? 2 : 3;

    for ($inCount=0; $inCount<$inCountFinal; $inCount++) {
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
        $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
        $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
        $obFormulario->ultimaLinha->commitCelula();
    }

    if ($inCountFinal==2) {
        $obRdbPosicao = new Radio;
        $obRdbPosicao->setName ("rdbPosicao");
        $obRdbPosicao->setValue("0-0");
        $obRdbPosicao->setChecked( (!$rdbPosicao) );
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
        $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
        $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obRdbPosicao );
        $obFormulario->ultimaLinha->commitCelula();
    }

    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
    $obFormulario->ultimaLinha->ultimaCelula->setColSpan(5);
    $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
    $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "FIMDECLARA" );
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->commitLinha();
    $stFuncao .= "FIMDECLARA".$stQuebra;

    if ( is_array($arFuncao['Corpo']) ) {
        foreach ($arFuncao['Corpo'] as $inChave=>$stValor) {
            $inCountNivel  = $inNivel    = $arFuncao['Corpo'][$inChave]['Nivel'];
            $stConteudo    = $arFuncao['Corpo'][$inChave]['Conteudo'];
            $stPagina      = 0;
            $stComplemento = '';

            if (substr(ltrim($stConteudo),0,2)=='SE' && substr($stConteudo,0,3)!='SEN') {
                $stPagina      = "FMPopupCondicao";
                $inNivelFuncao = $inNivel - 1;
                $stComplemento = '&nbsp;';
            } elseif (substr($stConteudo,0,2)=='EN') {
                $stPagina      = "FMPopupLaco";
                $inNivelFuncao = $inNivel - 1;
            } else {
                $stPagina      = "FMPopupAtribuicao";
                $inNivelFuncao = $inNivel;
                $stComplemento = '&stVariavelInicial='.'-'.substr($stConteudo,1,strpos($stConteudo,'<')-1);
            }

            $inNivelFuncao = ($inNivelFuncao>0) ? $inNivelFuncao : 0;
            $obAcaoExcluir = new Acao;
            $obAcaoExcluir->setAcao("EXCLUIR16px");
            $obAcaoExcluir->setLink("JavaScript:excluiDado('excluiLinhasCorpo','&stPosicao=$inChave-$inNivelFuncao');");

            $obAcaoAlterar = new Acao;
            $obAcaoAlterar->setAcao("ALTERAR16px");

            $obAcaoAlterar->setLink("JavaScript:AbrePopupAcao('".$stPagina."','alterar','&stPosicao=".$inChave."-".$inNivelFuncao."','".$stComplemento."');");
            $obFormulario->addLinha();
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
            $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
            if($stPagina)
                $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obAcaoExcluir );
            else
                $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
            $obFormulario->ultimaLinha->commitCelula();

            if ( substr($stConteudo,0,3) != 'SEN' && substr($stConteudo,0,3) != 'FIM') {
                $obFormulario->ultimaLinha->addCelula();
                $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
                $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
                if($stPagina)
                    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obAcaoAlterar );
                else
                    $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                $obFormulario->ultimaLinha->commitCelula();
            } else {
                $obFormulario->ultimaLinha->addCelula();
                $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
                $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
                $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
                $obFormulario->ultimaLinha->commitCelula();
            }

            //Verifica se não é estrutura condicional ou laço
            if (substr(ltrim($stConteudo),0,2)=='SE' || substr($stConteudo,0,2)=='EN') {
                $inCountNivel--;
            }
            for ($inCount=0; $inCount<($inCountNivel); $inCount++) {
                $stConteudo = "&nbsp;&nbsp;&nbsp;&nbsp;".$stConteudo;
            }
            $obRdbPosicao = new Radio;
            $obRdbPosicao->setName ("rdbPosicao");
            $obRdbPosicao->setValue( ($inChave+1)."-".$inNivel );

            $obRdbPosicao->obEvento->setOnClick("$('td_".($inChave+1)."').style.backgroundColor = '#F4F4F4';");
            $obRdbPosicao->obEvento->setOnBlur("$('td_".($inChave+1)."').style.backgroundColor = '#FFFFFF';");

            if ($inChaveChecked == $inChave+1) {
                $obRdbPosicao->setChecked( true );
            } else {
                $obRdbPosicao->setChecked( false );
            }
            // Muda a cor da linha para facilitar a visualização quando for muito extensa.
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
            $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
            $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obRdbPosicao );
            $obFormulario->ultimaLinha->commitCelula();
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
            $obFormulario->ultimaLinha->ultimaCelula->setId("td_".($inChave+1));
            $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
            $obFormulario->ultimaLinha->ultimaCelula->addConteudo( $stConteudo );
            $obFormulario->ultimaLinha->commitCelula();

            $obAcaoSubir = new Acao;
            $obAcaoSubir->setAcao("SUBIR15px");
            $obAcaoSubir->setLink("JavaScript:excluiDado('subir','&stPosicao=$inChave-$inNivelFuncao');");
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(1);
            $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
            if(($stPagina) && ($inChave>0))
                $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obAcaoSubir );
            else
                $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
            $obFormulario->ultimaLinha->commitCelula();

            $obAcaoBaixar = new Acao;
            $obAcaoBaixar->setAcao("BAIXAR15px");
            $obAcaoBaixar->setLink("JavaScript:excluiDado('baixar','&stPosicao=$inChave-$inNivelFuncao');");
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(1);
            $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
            if(($stPagina) && $inChave<(count($arFuncao['Corpo'])-1))
                $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obAcaoBaixar  );
            else
                $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
            $obFormulario->ultimaLinha->commitCelula();

            $obFormulario->commitLinha();
            $stFuncao .= str_replace("&nbsp;",' ',$stConteudo).$stQuebra;
        }
    }

    $obFormulario->addLinha();
    if ($arFuncao['RetornoVar']) {
        for ($inCount=0; $inCount<3; $inCount++) {
            $obFormulario->ultimaLinha->addCelula();
            $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
            $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
            $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
            $obFormulario->ultimaLinha->commitCelula();
        }
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
        $obFormulario->ultimaLinha->ultimaCelula->setColSpan(5);
        $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
        $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "RETORNA " . $arFuncao['RetornoVar'] . ";" );
        $obFormulario->ultimaLinha->commitCelula();
        $obFormulario->commitLinha();
        $stFuncao .= "RETORNA ".$arFuncao['RetornoVar'].";".$stQuebra;
    }

    $obFormulario->addLinha();
    for ($inCount=0; $inCount<3; $inCount++) {
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setWidth(2);
        $obFormulario->ultimaLinha->ultimaCelula->setClass("botao");
        $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
        $obFormulario->ultimaLinha->commitCelula();
    }
    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setWidth(99);
    $obFormulario->ultimaLinha->ultimaCelula->setColSpan(5);
    $obFormulario->ultimaLinha->ultimaCelula->setClass("fonte_programa");
    $obFormulario->ultimaLinha->ultimaCelula->addConteudo( "FIMFUNCAO" );
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->commitLinha();
    $stFuncao .= "FIMFUNCAO".$stQuebra;
    $obFormulario->montaInnerHTML();

    $this->setCorpoLN( $stFuncao );
    $this->ln2pl( $stFuncao );

    return $obFormulario->getHTML();
}

function ln2pl()
{
    $stLinguagem = $this->getCorpoLN();
    $arParametros = array();
    for ($inCount=0; $inCount<strlen($stLinguagem); $inCount++) {
        $stPalavra = "";
        $inChar = ord($stLinguagem[$inCount]);
        if ( $inChar == 58 and ord($stLinguagem[$inCount + 1])== 58) {//dois pontos
            $stPalavra .= "::";
            $inCount = $inCount + 2;
            $inChar  = ord($stLinguagem[$inCount]);
        } elseif ($inChar == 34) { //Aspas
            do {
                $stPalavra .= $stLinguagem[$inCount++];
                $inChar = ord($stLinguagem[$inCount]);
                if ($inChar == 35) {//#
                    $stPalavra .= "\'\' || ";
                    $inChar = ord($stLinguagem[++$inCount]);
                    while ( ($inChar >= 65 AND $inChar <= 90) OR ($inChar >= 97 AND $inChar <= 122) OR ($inChar >= 48 AND $inChar <= 57) ) {//letras e numeros
                        $stPalavra .= $stLinguagem[$inCount++] ;//tirar upper
                        $inChar = ord($stLinguagem[$inCount]);
                    }
                    $stPalavra .= " || \'\'";
                } else {
                    while ( ($inChar >= 65 AND $inChar <= 90) OR ($inChar >= 97 AND $inChar <= 122) OR ($inChar >= 48 AND $inChar <= 57) ) {//letras e numeros
                        $stPalavra .=  $stLinguagem[$inCount++] ;//tirar upper
                        $inChar = ord($stLinguagem[$inCount]);
                    }
                }
            } while ( $inChar != 34 );

            switch ($stPalavra) {
                case '"INTEIRO'     : $stPalavra = " INTEGER";    break;
                case '"TEXTO'       : $stPalavra = " VARCHAR";    break;
                case '"BOOLEANO'    : $stPalavra = " BOOLEAN";    break;
                case '"NUMERICO'    : $stPalavra = " NUMERIC";    break;
                case '"DATA'        : $stPalavra = " DATE";       break;
                case '"VAZIO'       : $stPalavra = " "; break;
                }

            $stPalavra = "\'\'".substr($stPalavra,1,strlen($stPalavra)-1)."\'\'";

            $inCount++;
        } elseif ( $inChar == 45 && ord($stLinguagem[$inCount+1]) == 45 ) { //Comentário --
            do {
                $stPalavra .= $stLinguagem[$inCount++];
                $inChar = ord($stLinguagem[$inCount]);
            } while ( $inChar != 13 && ord($stLinguagem[$inCount+1]) != 10 );
        } elseif ($inChar == 35) {//#
            //Variáveis
            while ( ($inChar >= 65 AND $inChar <= 90) OR ($inChar >= 97 AND $inChar <= 122) OR ($inChar == 35) ) {
                if ($inChar != 35) {
                    $stLinguagem[$inCount] =  $stLinguagem[$inCount] ;//tirar upper
                    $stPalavra .= $stLinguagem[$inCount];
                }
                $inCount++;
                $inChar = ord($stLinguagem[$inCount]);
                if ( !( ($inChar >= 65 AND $inChar <= 90) OR ($inChar >= 97 AND $inChar <= 122) ) ) {
                    $stPalavra = substr($stPalavra, 0, strlen($stPalavra)-1);
                    $inCount--;
                    break;
                }
            }

        } elseif ($inChar == 60) {
            //Atribuição
            if ( ord($stLinguagem[$inCount+1]) == 45 ) {
                $inCount=$inCount+2;
                $stPalavra .= ":=";
            }
        } elseif ($inChar == 58) {
            //Dois pontos
            $stLinguagem[$inCount] = " ";
        } elseif ( ($inChar >= 65 AND $inChar <= 90) OR ($inChar >= 97 AND $inChar <= 122) ) {
            //Palavra reservada
            while ( ($inChar >= 65 AND $inChar <= 90) OR ($inChar >= 97 AND $inChar <= 122) ) {
                $stLinguagem[$inCount] =  $stLinguagem[$inCount];//tirar upper
                $stPalavra .= $stLinguagem[$inCount];
                $inCount++;
                $inChar = ord($stLinguagem[$inCount]);
            }
            switch ($stPalavra) {
                case "INTEIRO"     : $stPalavra = "INTEGER";    break;
                case "TEXTO"       : $stPalavra = "VARCHAR";    break;
                case "BOOLEANO"    : $stPalavra = "BOOLEAN";    break;
                case "NUMERICO"    : $stPalavra = "NUMERIC";    break;
                case "DATA"        : $stPalavra = "DATE";       break;

                case "E"           : $stPalavra = "AND";        break;
                case "OU"          : $stPalavra = "OR";         break;

                case "VERDADEIRO"  : $stPalavra = "TRUE";       break;
                case "FALSO"       : $stPalavra = "FALSE";      break;

                case "NULO"        :
                   $stOperacao = substr( $stOut, strlen( $stOut ) - 4, strlen( $stOut ) - 3 );
                   if ( trim($stOperacao) == "!=" ) {
                       $stOut = substr( $stOut, 0,  strlen( $stOut ) - 4 );
                       $stPalavra = "IS NOT NULL";
                   } else {
                       $stOut = substr( $stOut, 0,  strlen( $stOut ) - 3 );
                       $stPalavra = "IS NULL";
                   }
                break;
                case "VAZIO"       : $stPalavra = "\'\'\'\'";         break;

                case "RETORNA"     : $stPalavra = "RETURN";     break;
                case "SE"          : $stPalavra = "IF";         break;
                case "ENTAO"       : $stPalavra = "THEN";       break;
                case "SENAO"       : $stPalavra = "ELSE";       break;
                case "FIMSE"       : $stPalavra = "END IF;";    break;
                case "ENQUANTO"    : $stPalavra = "WHILE";      break;
                case "FACA"        : $stPalavra = "LOOP";       break;
                case "FIMENQUANTO" : $stPalavra = "END LOOP;";  break;
                case "FIMFUNCAO"   : $stPalavra = "END;".chr(13).chr(10)." \' LANGUAGE \'plpgsql\'; ";   break;
                case "FUNCAO"      :
                    $stPalavra = "FUNCTION ";
                    $inParentese1 = strpos($stLinguagem,"(")+1;
                    $inParentese2 = strpos($stLinguagem,")");
                    $stPalavra .= $stNomeFuncao =  substr($stLinguagem, 6, ($inParentese1-1)-6) ;//tirar upper
                    $stParametros = substr($stLinguagem, $inParentese1, $inParentese2-$inParentese1 );
                    if(trim($stParametros))
                        $arParametros = explode("," ,$stParametros);
                    else
                        $arParametros = array();
                    $stPalavra .= "(";
                    foreach ($arParametros as $stParametro) {
                        $arParametro = explode(":",$stParametro);
                        switch (strtoupper(trim($arParametro[1]))) {
                            case "INTEIRO"    : $stPalavra .= "INTEGER,"; break;
                            case "TEXTO"      : $stPalavra .= "VARCHAR,"; break;
                            case "BOOLEANO"   : $stPalavra .= "BOOLEAN,"; break;
                            case "NUMERICO"   : $stPalavra .= "NUMERIC,"; break;
                            case "DATA"       : $stPalavra .= "DATE,";    break;
                        }
                    }
                    if(trim($stParametros))
                        $stPalavra = substr($stPalavra,0,strlen($stPalavra)-1);

                    $stPalavra .= ") RETURNS ";
                    switch (  trim(substr($stLinguagem, strpos($stLinguagem,"RETORNA")+7, strpos($stLinguagem,chr(13).chr(10)) - (strpos($stLinguagem,"RETORNA")+7) ))  ) {
                        case "INTEIRO"     : $stPalavra .= "INTEGER";    break;
                        case "TEXTO"       : $stPalavra .= "VARCHAR";    break;
                        case "BOOLEANO"    : $stPalavra .= "BOOLEAN";    break;
                        case "NUMERICO"    : $stPalavra .= "NUMERIC";    break;
                        case "DATA"        : $stPalavra .= "DATE";      break;
                    }
                    $stPalavra .= " as \' ";
                    $inCount = strpos($stLinguagem,chr(13).chr(10));
                break;
                case "FIMDECLARA"  : $stPalavra = "BEGIN";  break;
                case "DECLARA"     :
                    $stPalavra = "DECLARE".chr(13).chr(10);
                    foreach ($arParametros as $inIndice => $stParametro) {
                        $stParametro = trim($stParametro);
                        $stPalavra .= substr($stParametro,0,strpos($stParametro,":")) ;//tirar upper
                        $stPalavra .= " ALIAS FOR $".($inIndice+1).";".chr(13).chr(10);
                    }

                break;
            }
        } else {
            $stPalavra = "";
        }
        $stOut .= $stPalavra;
        $stOut .= $stLinguagem[$inCount];
    }
    $stOut = str_replace(chr(13).chr(10),"<br>",$stOut);
    $this->setCorpoPL( "<div align=\"left\"><pre>".$stOut."</pre></div>" );

    return $this->getCorpoPL();
}

}

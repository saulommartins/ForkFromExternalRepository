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
    * Classe de Regra de Negócio para Configuração da tesouraria
    * Data de Criação   : 01/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-10-23 15:50:33 -0300 (Seg, 23 Out 2006) $

    * Casos de uso: uc-02.04.01,uc-02.04.19 , uc-02.04.25
*/

/*
$Log$
Revision 1.22  2006/10/23 18:50:33  domluc
caso de uso atualizado

Revision 1.21  2006/10/23 16:35:59  domluc
Adicionado Busca da Configuração de Boletim

Revision 1.20  2006/07/05 20:38:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS       ."Transacao.class.php"             );
include_once ( CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaAssinatura.class.php"     );

/**
    * Classe de Regra de Assinatura
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaConfiguracao extends RConfiguracaoConfiguracao
{
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaAssinatura;
/*
    * @var Integer
    * @access Private
*/
var $inFormaComprovacao;
/*
    * @var Integer
    * @access Private
*/
var $inNumeracaoComprovacao;
/*
    * @var Integer
    * @access Private
*/
var $inViasComprovacao;
/*
    * @var Boolean
    * @access Private
*/
var $boReiniciarNumeracao;
/*
    * @var Array
    * @access Private
*/
var $arAssinatura;
/*
    * @var Object
    * @access Private
*/
var $roUltimaAssinatura;
/*
    * @var String
    * @access Private
*/
var $stDigitos;
/*
    * @var Boolean
    * @access Private
*/
var $boOcultarMovimentacoes;
/*
    * @var Boolean
    * @access Private
*/
var $boMultiplosBoletins;

/*
    * @access Public
    * @param Integer $valor
*/
function setFormaComprovacao($valor) { $this->inFormaComprovacao      = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setNumeracaoComprovacao($valor) { $this->inNumeracaoComprovacao  = $valor; }
/*
    * @access Public
    * @param Integer $valor
*/
function setViasComprovacao($valor) { $this->inViasComprovacao       = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setReiniciarNumeracao($valor) { $this->boReiniciarNumeracao    = $valor; }
/*
    * @access Public
    * @param Array $valor
*/
function setAssinatura($valor) { $this->arAssinatura            = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDigitos($valor) { $this->stDigitos            = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setOcultarMovimentacoes($valor) { $this->boOcultarMovimentacoes = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setMultiplosBoletins($valor) { $this->boMultiplosBoletins = $valor; }

/*
    * @access Public
    * @return Integer
*/
function getFormaComprovacao() { return $this->inFormaComprovacao;      }
/*
    * @access Public
    * @return Integer
*/
function getNumeracaoComprovacao() { return $this->inNumeracaoComprovacao;  }
/*
    * @access Public
    * @return Integer
*/
function getViasComprovacao() { return $this->inViasComprovacao;       }
/*
    * @access Public
    * @return Boolean
*/
function getReiniciarNumeracao() { return $this->boReiniciarNumeracao;    }
/*
    * @access Public
    * @return Array
*/
function getAssinatura() { return $this->arAssinatura;            }
/*
    * @access Public
    * @return String
*/
function getDigitos() { return $this->stDigitos;            }
/*
    * @access Public
    * @return Boolean
*/
function getOcultarMovimentacoes() { return $this->boOcultarMovimentacoes; }
/*
    * @access Public
    * @return Boolean
*/
function getMultiplosBoletins() { return $this->boMultiplosBoletins; }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaConfiguracao()
{
    parent::RConfiguracaoConfiguracao();
    $this->setCodModulo( 30 );

}

/*
    * Método para adicionar assinaturas
    * @access Public
*/
function addAssinatura()
{
    $this->arAssinatura[] = new RTesourariaAssinatura();
    $this->roUltimaAssinatura = $this->arAssinatura[ count( $this->arAssinatura ) -1 ];
}

/*
    * Método para salvar assinaturas
    * @access Public
    * @param Object $boTransacao Parâmetro de Transação
    * @return Object Objeto de Erro
*/
function salvarAssinatura($boTransacao = "")
{
    $obRTesourariaAssinatura = new RTesourariaAssinatura();
    $obRTesourariaAssinatura->setExercicio( $this->stExercicio );
    $obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimaAssinatura->obROrcamentoEntidade->getCodigoEntidade());
    $obErro = $obRTesourariaAssinatura->listar( $rsAssinatura, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsAssinatura->eof() ) {
            $obRTesourariaAssinatura->obRCGM->setNumCGM( $rsAssinatura->getCampo( 'numcgm' ) );
            $obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($rsAssinatura->getCampo('cod_entidade'));
            $obErro = $obRTesourariaAssinatura->excluir( $boTransacao );
            if ( $obErro->ocorreu() ) {
                break;
            }
            $rsAssinatura->proximo();
        }
        if ( !$obErro->ocorreu() ) {
            if ( is_array( $this->arAssinatura ) ) {
                foreach ($this->arAssinatura as $obRTesourariaAssinatura) {
                    $obErro = $obRTesourariaAssinatura->incluir( $boTransacao );
                    if( $obErro->ocorreu() )
                        break;
                }
            }
        }
    }

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arParametros = array( 'forma_comprovacao'       => 'inFormaComprovacao'
                              ,'numeracao_comprovacao'   => 'inNumeracaoComprovacao'
                              ,'numero_vias_comprovacao' => 'inViasComprovacao'
                              ,'reiniciar_comprovacao'   => 'boReiniciarNumeracao'
                              ,'ocultar_mov_conciliacao' => 'boOcultarMovimentacoes'
                              ,'digitos_autenticacao'    => 'stDigitos'   );
        foreach ($arParametros as $stParametro => $stParametroValor) {
            if ( (integer) $this->$stParametroValor >= 0 ) {
                $this->setParametro( $stParametro );
                $this->setValor( $this->$stParametroValor );
                $this->verificaParametro( $boExiste, $boTransacao );
                if ($boExiste) {
                    $obErro = parent::alterar( $boTransacao );
                } else {
                    $obErro = parent::incluir( $boTransacao );
                }
                if( $obErro->ocorreu() )
                    break;
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ( sizeof($this->getAssinatura()) ) {
                $obErro = $this->salvarAssinatura( $boTransacao );
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTEmpenhoEmpenho );

    return $obErro;
}

/**
    * Método para recuperar todas as assinaturas do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarAssinatura($boTransacao = "")
{
    $obRTesourariaAssinatura = new RTesourariaAssinatura();
    $obRTesourariaAssinatura->setExercicio( $this->stExercicio );
    $obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade( $this->roUltimaAssinatura->obROrcamentoEntidade->getCodigoEntidade());
    $obErro = $obRTesourariaAssinatura->listar( $rsAssinatura, '', $boTransacao );
    if ( !$obErro->ocorreu() and !$rsAssinatura->eof() ) {
        $this->arAssinatura = array();
        while ( !$rsAssinatura->eof() ) {
            $this->addAssinatura();
            $this->roUltimaAssinatura->setExercicio( $rsAssinatura->getCampo( 'exercicio' ) );
            $this->roUltimaAssinatura->setTipo( $rsAssinatura->getCampo( 'tipo' ) );
            $this->roUltimaAssinatura->setNumMatricula( $rsAssinatura->getCampo( 'num_matricula' ) );
            $this->roUltimaAssinatura->obRCGM->setNumCGM( $rsAssinatura->getCampo( 'numcgm' ) );
            $this->roUltimaAssinatura->obRCGM->setNomCGM( $rsAssinatura->getCampo( 'nom_cgm' ) );
            $this->roUltimaAssinatura->setCargo( $rsAssinatura->getCampo( 'cargo' ) );
            $this->roUltimaAssinatura->setSituacao( $rsAssinatura->getCampo( 'situacao' ) );
            $this->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade( $rsAssinatura->getCampo('cod_entidade'));
            $rsAssinatura->proximo();
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarTesouraria($boTransacao = "")
{
    $this->setParametro( 'forma_comprovacao' );
    $obErro = parent::consultar( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->inFormaComprovacao = $this->getValor();

        $this->setParametro( 'numeracao_comprovacao' );
        $obErro = parent::consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->inNumeracaoComprovacao = $this->getValor();

            $this->setParametro( 'reiniciar_comprovacao' );
            $obErro = parent::consultar( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->boReiniciarNumeracao = $this->getValor();

                $this->setParametro( 'numero_vias_comprovacao' );
                $obErro = parent::consultar( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->inViasComprovacao = $this->getValor();

                    $this->setParametro( 'digitos_autenticacao' );
                    $obErro = parent::consultar( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->stDigitos = $this->getValor();

                        $this->setParametro( 'ocultar_mov_conciliacao' );
                        $obErro = parent::consultar( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->boOcultarMovimentacoes = $this->getValor();

                            $this->setParametro( 'multiplos_boletim' );
                            $obErro = parent::consultar( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->boMultiplosBoletins = (boolean) $this->getValor();
                            }
                        }
                    }
                }
            }
        }
    }

    return $obErro;
}

}

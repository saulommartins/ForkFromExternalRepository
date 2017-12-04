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
* Classe de Regra de Negócio Folha Pagamento Sindicato
* Data de Criação   : 26/11/2004

* @author Analista: Leandro Oliveira.
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.03
Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSindicato.class.php"                           );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSindicatoFuncao.class.php"                     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                                                   );
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                 );

class RFolhaPagamentoSindicato
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodSindicato;

    /**
    * @var Objeto
    * @access Private
    */
    public $obTFolhaPagamentoSindicato;

    /**
    * @var Objeto
    * @access Private
    */
    public $obTFolhaPagamentoSindicatoPessoal;

    /**
    * @var Objeto
    * @access Private
    */
    public $obTransacao;

    /**
    * @var Objeto
    * @access Private
    */
    public $obRCGM;

    /**
    * @access Public
    * @param Object $valor
    */
    public $inDataBase;

    /**
    * @access Public
    * @param Object $valor
    */
    public $obRFolhaPagamentoEvento;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodSindicato($valor) { $this->inCodSindicato      = $valor; }

    /**
    * @access Private
    * @param Object $valor
    */
    public function setTFolhaPagamentoSindicato($valor) { $this->obTFolhaPagamentoSindicato = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao                 = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setRCGM($valor) { $this->obRCGM                      = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDataBase($valor) { $this->inDataBase                      = $valor; }

     /**
    * @access Public
    * @param Object $valor
    */
    public function setobREvento(&$objeto) { $this->obRFolhaPagamentoEvento  = $objeto; }
    public function getobREvento() { return $this->obRFolhaPagamentoEvento;     }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getCodSindicato() { return $this->inCodSindicato;                  }

    /**
    * @access Public
    * @param Object $valor
    */

    /**
    * @access Public
    * @param Object $valor
    */
    public function getTFolhaPagamentoSindicato() { return $this->obTFolhaPagamentoSindicato;                  }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getTFolhaPagamentoSindicatoFuncao() { return $this->obTFolhaPagamentoSindicatoFuncao;                  }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getTransacao() { return $this->obTransacao;                          }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getRCGM() { return $this->obRCGM;                               }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getRFuncao() { return $this->obRFuncao;                               }

    /**
    * @access Public
    * @param Object $valor
    */
    public function getDataBase() { return $this->inDataBase;                           }

    /**
    * Método Construtor
    * @access Private
    */
    public function RFolhaPagamentoSindicato()
    {
        $this->setTFolhaPagamentoSindicato  ( new TFolhaPagamentoSindicato );
        $this->setTransacao                 ( new Transacao                );
        $this->setRCGM                      ( new RCGM                     );
        $this->setobREvento                 ( new RFolhaPagamentoEvento    );
    }

    /**
    * Cadastra desconto para o sindicato
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function incluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTFolhaPagamentoSindicato->setDado ( "numcgm"   ,  $this->obRCGM->getNumCGM()                     );
            $this->obTFolhaPagamentoSindicato->setDado ( "data_base",  $this->getDataBase()                           );
            $this->obTFolhaPagamentoSindicato->setDado ( "cod_evento", $this->obRFolhaPagamentoEvento->getCodEvento() );
            $this->listar($rsSindicatos);

            // trocar esta busca por uma consulta em SQL
            while ( !$rsSindicatos->eof() ) {
                if ( $rsSindicatos->getCampo("numcgm") == $this->obRCGM->getNumCGM() ) {
                    $obErro->setDescricao ("Sindicato já cadastrado");

                    return $obErro;
                    break;
                }
                $rsSindicatos->proximo();
            }

            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTFolhaPagamentoSindicato->inclusao( $boTransacao );
           }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoSindicato );

        return $obErro;
    }

    public function alterar($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao ( $boFlagTransacao, $boTransacao );
        $this->obTFolhaPagamentoSindicato->setDado  ( "numcgm"  ,   $this->obRCGM->getNumCGM()                     );
        $this->obTFolhaPagamentoSindicato->setDado  ( "data_base",  $this->getDataBase()                           );
        $this->obTFolhaPagamentoSindicato->setDado  ( "cod_evento", $this->obRFolhaPagamentoEvento->getCodEvento() );

        $obErro = $this->obTFolhaPagamentoSindicato->alteracao( $boTransacao );

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoSindicato );

        return $obErro;
    }

    /**
    * Executa um recuperaPorChave na classe Persistente Sindicato
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultar($boTransacao = "")
    {
        if( $this->obRCGM->getNumCGM() )
            $stFiltro .= " AND S.numcgm = ". $this->obRCGM->getNumCGM();

        $obErro = $this->obTFolhaPagamentoSindicato->recuperaRelacionamento( $rsFolhaPagamentoSindicato, $stFiltro, $stOrder = "", $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obRCGM->setNumCGM ( $rsFolhaPagamentoSindicato->getCampo( "numcgm" )   );
            $this->setDataBase       ( $rsFolhaPagamentoSindicato->getCampo( "data_base") );
            // buscando os dados do evento

            $this->obRFolhaPagamentoEvento->setCodEvento( $rsFolhaPagamentoSindicato->getCampo( "cod_evento") );
            $this->obRFolhaPagamentoEvento->consultarEvento();
        }

        return $obErro;
    }

    /**
    * Exclui Sindicato
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function excluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTFolhaPagamentoSindicato->setDado( "numcgm"  , $this->obRCGM->getNumCGM() );
            $this->consultarSindicato( $rsSindicato );
            if ( !$obErro->ocorreu() ) {
                if ( $rsSindicato->getCampo("sindicato_servidor") == "" ) {
                    if ( !$obErro->ocorreu() ) {
                        $this->obTFolhaPagamentoSindicato->setDado( "numcgm"  , $this->obRCGM->getNumCGM() );
                        $obErro = $this->obTFolhaPagamentoSindicato->exclusao( $boTransacao );
                    }
                } else {
                    $obErro->setDescricao ("Sindicato não pode ser excluído, porque está vinculado a um servidor.");

                    return $obErro;
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoSindicato );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Sindicato
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsFolhaPagamentoSindicato, $stFiltro = "", $boTransacao = "")
    {
        $obErro = $this->obTFolhaPagamentoSindicato->recuperaRelacionamento( $rsFolhaPagamentoSindicato , $stFiltro ,"" ,$boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Sindicato
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultarSindicato(&$rsFolhaPagamentoSindicato, $stFiltro = "", $boTransacao = "")
    {
        $this->obTFolhaPagamentoSindicato->setDado( "numcgm", $this->obRCGM->getNumCGM() );
        $obErro = $this->obTFolhaPagamentoSindicato->RecuperaCGMSindicato( $rsFolhaPagamentoSindicato,"","",$boTransacao = "" );

        return $obErro;
    }

}

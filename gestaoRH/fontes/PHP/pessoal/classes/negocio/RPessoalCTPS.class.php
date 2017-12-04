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
    * Classe de Regra de Negócio Pessoal CTPS
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCTPS.class.php"                          );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidorCTPS.class.php"                  );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                           );

/**
    * Classe de Regra de Negócio Pesssoal CTPS
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/

class RPessoalCTPS
{
    /**
    * @var Reference Object
    * @access Private
    */
    public $roPessoalServidor;

    /**
    * @var Array
    * @access Private
    */
    public $arCTPS;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodCTPS;

    /**
    * @var Integer
    * @access Private
    */
    public $inNumero;

    /**
    * @var date
    * @access Private
    */
    public $inCodUF;

    /**
    * @var date
    * @access Private
    */
    public $dtEmissao;

    /**
    * @var String
    * @access Private
    */
    public $stOrgaoExpedidor;

    /**
    * @var String
    * @access Private
    */
    public $stSerie;

    /**
    * @var Integer
    * @access Private
    */
    public $obUltimaCTPS;

    /**
    * @var Integer
    * @access Private
    */
    public $obTPessoalCTPS;

    /**
    * @var Integer
    * @access Private
    */
    public $obTPessoalServidorCTPS;

    /**
    * @var Object
    * @access Private
    */
    public $obRPessoalServidor;

    /**
    * @var Object
    * @access Private
    */
    public $obTransacao;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setUltimaCTPS($valor) { $this->obUltimaCTPS       = $valor  ; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCTPS($valor) { $this->arCTPS         = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodCTPS($valor) { $this->inCodCTPS         = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setNumero($valor) { $this->inNumero          = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setEmissao($valor) { $this->dtEmissao  = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setOrgaoExpedidor($valor) { $this->stOrgaoExpedidor    = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setSerie($valor) { $this->stSerie    = $valor;                   }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setRServidor($valor) { $this->obRPessoalServidor         = $valor;   }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalCTPS($valor) { $this->obTPessoalCTPS             = $valor;   }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalServidorCTPS($valor) { $this->obTPessoalServidorCTPS     = $valor;   }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setUfCTPS($valor) { $this->inCodUF             = $valor;   }

    /**
    * @access Public
    * @param Object $valor
    */

    public function setTransacao($valor) { $this->obTransacao = $valor;                  }

    public function getCodCTPS() { return $this->inCodCTPS;                     }
    public function getUltimaCTPS() { return $this->obUltimaCTPS;                  }
    public function getCTPS() { return $this->arCTPS;                        }
    public function getNumero() { return $this->inNumero;                      }
    public function getEmissao() { return $this->dtEmissao;                     }
    public function getOrgaoExpedidor() { return $this->stOrgaoExpedidor;              }
    public function getSerie() { return $this->stSerie;                       }
    public function getTPessoalCTPS() { return $this->obTPessoalCTPS;                }
    public function getTPessoalServidorCTPS() { return $this->obTPessoalServidorCTPS;        }
    public function getRPessoalServidor() { return $this->obRPessoalServidor;            }
    public function getTransacao() { return $this->obTransacao;                   }
    public function getCodUF() { return $this->inCodUF;                       }

    public function RPessoalCTPS(&$roPessoalServidor)
    {
        $this->setTransacao              ( new Transacao               );
        $this->setTPessoalCTPS           ( new TPessoalCTPS            );
        $this->setTPessoalServidorCTPS   ( new TPessoalServidorCTPS    );
        $this->setCTPS                   = array();
        $this->roPessoalServidor         = &$roPessoalServidor;

    }

function addCTPS($valor)
{
    $this->arCTPS = $valor;
}
/**
    * Adiciona o objeto do tipo Nivel ao array
    * @access Public
*/
function commitCTPS()
{
    $arElementos   = $this->getCTPS();
    $arElementos[] = $this->getUltimaCTPS();
    $this->setCTPS( $arElementos );
}

    /**
    * Cadastra CTPS Servidor
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function incluirCTPS($boTransacao = "")
    {
        $boFlagTransacao = false;
        $rsCTPS = new RecordSet;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalCTPS->proximoCod( $inCodCTPS, $boTransacao );
            $this->setCodCTPS( $inCodCTPS );
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalCTPS->setDado("cod_ctps"       , $this->getCodCTPS()           );
                $this->obTPessoalCTPS->setDado("numero"         , $this->getNumero()            );
                $this->obTPessoalCTPS->setDado("orgao_expedidor", $this->getOrgaoExpedidor()    );
                $this->obTPessoalCTPS->setDado("serie"          , $this->getSerie()             );
                $this->obTPessoalCTPS->setDado("dt_emissao"     , $this->getEmissao()           );
                $this->obTPessoalCTPS->setDado("uf_expedicao"   , $this->getCodUF()             );
                $obErro = $this->obTPessoalCTPS->inclusao( $boTransacao );

            }
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalServidorCTPS->setDado("cod_servidor",$this->roPessoalServidor->getCodServidor());
                $this->obTPessoalServidorCTPS->setDado("cod_ctps"    ,$this->getCodCTPS()                       );
                $obErro = $this->obTPessoalServidorCTPS->inclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCTPS );

        return $obErro;
    }

    /**
    * Altera CTPS Servidor
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function alterarCTPS($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if (!$obErro->ocorreu()) {
            $this->obTPessoalCTPS->setDado("cod_ctps"       , $this->getCodCTPS()               );
            $this->obTPessoalCTPS->setDado("numero"         , $this->getNumero()                );
            $this->obTPessoalCTPS->setDado("orgao_expedidor", $this->getOrgaoExpedidor()        );
            $this->obTPessoalCTPS->setDado("serie"          , $this->getSerie()                 );
            $this->obTPessoalCTPS->setDado("dt_emissao"     , $this->getEmissao()               );
            $this->obTPessoalCTPS->setDado("uf_expedicao"   , $this->getCodUF()                 );
            $obErro = $this->obTPessoalCTPS->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCTPS );

        return $obErro;
    }

    /**
    * Executa um recuperaPorChave na classe Persistente Empresa Transporte
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultarCTPS($boTransacao = "")
    {
        if ( $this->obRPessoalServidor->getCodServidor() ) {
            $stFiltro .= " AND servidor_ctps.cod_servidor = ".$this->obRPessoalServidor->getCodServidor();
        }
        $obErro = $this->obTPessoalCTPS->recuperaRelacionamento( $rsPessoalCTPS, $stFiltro,"" ,$boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRValetransporteEmpresaTransporte->obRCGM->setNumCGM ( $rsPessoalCTPS->getCampo( "numcgm" )  );
            $this->obRValetransporteEmpresaTransporte->obRCGM->setNomCGM ( $rsPessoalCTPS->getCampo( "nom_cgm" ) );
            $this->setCodCTPS               ( $rsPessoalCTPS->getCampo( "cod_ctps"       )   );
            $this->setNumero                ( $rsPessoalCTPS->getCampo( "numero"         )   );
            $this->setEmissao               ( $rsPessoalCTPS->getCampo( "emissao"        )   );
            $this->setOrgaoExpedidor        ( $rsPessoalCTPS->getCampo( "orgaoExpedidor" )   );
            $this->setSerie                 ( $rsPessoalCTPS->getCampo( "serie"          )   );
            $this->setUfCTPS                ( $rsPessoalCTPS->getCampo( "inCodUF"        )   );
        }

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Empresa Transporte
    * @access Public
    * @param  Object $rsListaCTPS retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarCTPS(&$rsRecordSet, $boTransacao = "")
    {
        if ( $this->roPessoalServidor->getCodServidor() ) {
            $stFiltro .= " AND sc.cod_servidor = ".$this->roPessoalServidor->getCodServidor();
        }
        $obErro = $this->obTPessoalCTPS->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }

    public function excluirCTPS($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalServidorCTPS->setDado( "cod_ctps", $this->getCodCTPS() );
            $obErro = $this->obTPessoalServidorCTPS->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalCTPS->setDado( "cod_ctps", $this->getCodCTPS());
                $obErro = $this->obTPessoalCTPS->exclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCTPS );

        return $obErro;
    }

}

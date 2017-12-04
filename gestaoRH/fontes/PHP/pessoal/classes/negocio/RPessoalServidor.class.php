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
    * Classe de Regra de Negócio Servidor
    * Data de Criação   : 14/12/2004

    * @author Desenvolvedor: Rafael Almeida

    * Caso de uso: uc-04.04.07

    * @package URBEM
    * @subpackage Regra

    $Id: RPessoalServidor.class.php 66173 2016-07-26 14:57:24Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalCID.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalConselho.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalDependente.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalCTPS.class.php";

class RPessoalServidor
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Integer
*/
var $inCodServidor;
/**
   * @access Private
   * @var Integer
*/
var $inCodRaca;
/**
   * @access Private
   * @var Integer
*/
var $inCodEstadoCivil;
/**
   * @access Private
   * @var Integer
*/
var $inCodUF;
/**
   * @access Private
   * @var Integer
*/
var $inCodMunicipio;
/**
   * @access Private
   * @var Integer
*/
var $inCodEdital;
/**
   * @access Private
   * @var Integer
*/
var $inCodRais;
/**
   * @access Private
   * @var String
*/
var $stPisPasep;
/**
   * @access Private
   * @var Date
*/
var $dtDataPisPasep;
/**
   * @access Private
   * @var Date
*/
var $dtNascimento;
/**
   * @access Private
   * @var Integer
*/
var $inCarteiraReservista;
/**
   * @access Private
   * @var String
*/
var $stCategoriaReservista;
/**
   * @access Private
   * @var Integer
*/
var $inOrigemReservista;
/**
   * @access Private
   * @var String
*/
var $stNrTituloEleitor;
/**
   * @access Private
   * @var String
*/
var $stZonaTitulo;
/**
   * @access Private
   * @var String
*/
var $stSecaoTitulo;
/**
   * @access Private
   * @var String
*/
var $stCaminhoFoto;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalDependente;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalDependente;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalCTPS;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalCTPS;
/**
   * @access Private
   * @var Object
*/
var $obRCGMPessoaFisica;
/**
   * @access Private
   * @var String
*/
var $stNomePai;
/**
   * @access Private
   * @var String
*/
var $stNomeMae;
/**
   * @access Private
   * @var Object
*/
var $obRCGMPessoaFisicaConjuge;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalCID;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalMunicipio;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalConselho;
/**
   * @access Private
   * @var Object
*/
var $arArquivosDocumentosDigitais;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao            = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodServidor($valor) { $this->inCodServidor          = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodRaca($valor) { $this->inCodRaca              = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodEstadoCivil($valor) { $this->inCodEstadoCivil       = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodUF($valor) { $this->inCodUF                = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodMunicipio($valor) { $this->inCodMunicipio         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodEdital($valor) { $this->inCodEdital            = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodRais($valor) { $this->inCodRais              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setPisPasep($valor) { $this->stPisPasep             = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataPisPasep($valor) { $this->dtDataPisPasep         = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataNascimento($valor) { $this->dtNascimento           = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setDataLaudo($valor) { $this->dtLaudo           = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setCarteiraReservista($valor) { $this->inCarteiraReservista   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCategoriaReservista($valor) { $this->stCategoriaReservista  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setOrigemReservista($valor) { $this->inOrigemReservista     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNrTituloEleitor($valor) { $this->stNrTituloEleitor      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setZonaTitulo($valor) { $this->stZonaTitulo           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSecaoTitulo($valor) { $this->stSecaoTitulo          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCaminhoFoto($valor) { $this->stCaminhoFoto          = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalDependente($valor) { $this->arRPessoalDependente   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalDependente(&$valor) { $this->roRPessoalDependente  = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalCTPS($valor) { $this->arRPessoalCTPS         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalCTPS(&$valor) { $this->roRPessoalCTPS        = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCGMPessoaFisica($valor) { $this->obRCGMPessoaFisica     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomePai($valor) { $this->stNomePai              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeMae($valor) { $this->stNomeMae              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCGMPessoaFisicaConjuge($valor) { $this->obRCGMPessoaFisicaConjuge= $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalCID($valor) { $this->obRPessoalCID          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalMunicipio($valor) { $this->obRPessoalMunicipio    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalConselho($valor) { $this->obRPessoalConselho     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setArquivosDocumentos($valor) { $this->arArquivosDocumentosDigitais     = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;            }
/**
    * @access Public
    * @return Integer
*/
function getCodServidor() { return $this->inCodServidor;          }
/**
    * @access Public
    * @return Integer
*/
function getCodRaca() { return $this->inCodRaca;              }
/**
    * @access Public
    * @return Integer
*/
function getCodEstadoCivil() { return $this->inCodEstadoCivil;       }
/**
    * @access Public
    * @return Integer
*/
function getCodUF() { return $this->inCodUF;                }
/**
    * @access Public
    * @return Integer
*/
function getCodMunicipio() { return $this->inCodMunicipio;         }
/**
    * @access Public
    * @return Integer
*/
function getCodEdital() { return $this->inCodEdital;            }
/**
    * @access Public
    * @return Integer
*/
function getCodRais() { return $this->inCodRais;              }
/**
    * @access Public
    * @return String
*/
function getPisPasep() { return $this->stPisPasep;             }
/**
    * @access Public
    * @return Date
*/
function getDataPisPasep() { return $this->dtDataPisPasep;         }
/**
    * @access Public
    * @return Date
*/
function getDataNascimento() { return $this->dtNascimento;           }
/**
    * @access Public
    * @return Integer
*/
function getDataLaudo() { return $this->dtLaudo;           }
/**
    * @access Public
    * @return Date
*/
function getCarteiraReservista() { return $this->inCarteiraReservista;   }
/**
    * @access Public
    * @return String
*/
function getCategoriaReservista() { return $this->stCategoriaReservista;  }
/**
    * @access Public
    * @return Integer
*/
function getOrigemReservista() { return $this->inOrigemReservista;     }
/**
    * @access Public
    * @return String
*/
function getNrTituloEleitor() { return $this->stNrTituloEleitor;      }
/**
    * @access Public
    * @return String
*/
function getZonaTitulo() { return $this->stZonaTitulo;           }
/**
    * @access Public
    * @return String
*/
function getSecaoTitulo() { return $this->stSecaoTitulo;          }
/**
    * @access Public
    * @return String
*/
function getCaminhoFoto() { return $this->stCaminhoFoto;          }
/**
    * @access Public
    * @return Array
*/
function getARRPessoalDependente() { return $this->arRPessoalDependente;   }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalDependente() { return $this->roRPessoalDependente;   }
/**
    * @access Public
    * @return Array
*/
function getARRPessoalCTPS() { return $this->arRPessoalCTPS;         }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalCTPS() { return $this->roRPessoalCTPS;         }
/**
    * @access Public
    * @return Object
*/
function getRCGMPessoaFisica() { return $this->obRCGMPessoaFisica;     }
/**
    * @access Public
    * @return String
*/
function getNomePai() { return $this->stNomePai;              }
/**
    * @access Public
    * @return String
*/
function getNomeMae() { return $this->stNomeMae;              }
/**
    * @access Public
    * @return Object
*/
function getRCGMPessoaFisicaConjuge() { return $this->obRCGMPessoaFisicaConjuge;  }
/**
    * @access Public
    * @return Object
*/
function getRPessoalCID() { return $this->obRPessoalCID;          }
/**
    * @access Public
    * @return Object
*/
function getRPessoalMunicipio() { return $this->obRPessoalMunicipio;    }
/**
    * @access Public
    * @return Object
*/
function getRPessoalConselho() { return $this->obRPessoalConselho;     }
/**
    * @access Public
    * @return Object
*/
function getArquivosDocumentos() { return $this->arArquivosDocumentosDigitais;     }

    /**
    * Mâtodo Construtor
    * @access Private
    */
    public function RPessoalServidor()
    {
        $this->setTransacao                         ( new Transacao                             );
        $this->setRCGMPessoaFisica                  ( new RCGMPessoaFisica                      );
        $this->setRPessoalConselho                  ( new RPessoalConselho                      );
        $this->setRPessoalCID                       ( new RPessoalCID                           );
        $this->setRCGMPessoaFisicaConjuge           ( new RCGM                                  );
    }

    /**
    * Cadastra Servidor
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o mâtodo
    *
    **/
    public function incluirServidor($boTransacao = "")
    {
        $obErro = new Erro;
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorReservista.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorPisPasep.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorConjuge.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorCid.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorContratoServidor.class.php";
        include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDocumentoDigital.class.php";

        $obTPessoalServidor                 = new TPessoalServidor;
        $obTPessoalServidorReservista       = new TPessoalServidorReservista;
        $obTPessoalServidorPisPasep         = new TPessoalServidorPisPasep;
        $obTPessoalServidorConjuge          = new TPessoalServidorConjuge;
        $obTPessoalServidorCid              = new TPessoalServidorCid;
        $obTPessoalServidorContratoServidor = new TPessoalServidorContratoServidor;
        $obTCGMPessoaFisica                 = new TCGMPessoaFisica;

        if ( !$obErro->ocorreu() and ($this->getCarteiraReservista() or $this->getCategoriaReservista() or $this->getOrigemReservista()) ) {
            if ( $this->getCarteiraReservista() == "" ) {
                $obErro->setDescricao("Campo Certificado de Reservista da guia Documentação inválido!()");
            }
            if ( $this->getCategoriaReservista() == "" ) {
                $obErro->setDescricao("Campo Categoria do Certificado da guia Documentação inválido!()");
            }
            if ( $this->getOrigemReservista() == "") {
                $obErro->setDescricao("Campo Órgão Expedidor do Certificado da guia Documentação inválido!()");
            }
        }

        if ( !$obErro->ocorreu() and $this->getDataNascimento()) {
            $stFiltro = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCgm();

            $obErro = $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);

            if ( !$obErro->ocorreu() ){
                $obTCGMPessoaFisica->setDado("dt_nascimento",       $this->getDataNascimento());
                $obTCGMPessoaFisica->setDado("numcgm",              $this->obRCGMPessoaFisica->getNumCgm());
                $obTCGMPessoaFisica->setDado("cod_categoria_cnh",   $rsCGM->getCampo('cod_categoria_cnh'));
                $obTCGMPessoaFisica->setDado("orgao_emissor",       $rsCGM->getCampo('orgao_emissor'));
                $obTCGMPessoaFisica->setDado("cpf",                 $rsCGM->getCampo('cpf'));
                $obTCGMPessoaFisica->setDado("num_cnh",             $rsCGM->getCampo('num_cnh'));
                $obTCGMPessoaFisica->setDado("cod_nacionalidade",   $rsCGM->getCampo('cod_nacionalidade'));

                $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
            }
        }

        if ( !$obErro->ocorreu() and $this->obRCGMPessoaFisica->getCPF() ) {
            $inCPF = str_replace(".","", $this->obRCGMPessoaFisica->getCPF());
            $inCPF = str_replace("-","", $inCPF);
            $stFiltro = " WHERE cpf = '".$inCPF."'";
            $obErro = $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);

            if ( !$obErro->ocorreu() ){
                if ( $rsCGM->getNumLinhas() > 0 ) {
                    $obErro->setDescricao("CPF já cadastrado para outro servidor.");
                } else {
                    $stFiltro  = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCgm();
                    $obErro = $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);

                    if ( !$obErro->ocorreu() ){
                        $obTCGMPessoaFisica->setDado("dt_nascimento",       $rsCGM->getCampo('dt_nascimento'));
                        $obTCGMPessoaFisica->setDado("numcgm",              $this->obRCGMPessoaFisica->getNumCgm());
                        $obTCGMPessoaFisica->setDado("cod_categoria_cnh",   $rsCGM->getCampo('cod_categoria_cnh'));
                        $obTCGMPessoaFisica->setDado("orgao_emissor",       $rsCGM->getCampo('orgao_emissor'));
                        $obTCGMPessoaFisica->setDado("cpf",                 $inCPF);
                        $obTCGMPessoaFisica->setDado("num_cnh",             $rsCGM->getCampo('num_cnh'));
                        $obTCGMPessoaFisica->setDado("cod_nacionalidade",   $rsCGM->getCampo('cod_nacionalidade'));

                        $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
                    }
                }
            }
        }

        // inclui dados para o servidor
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTPessoalServidor->proximoCod( $inCodServidor , $boTransacao );
            $this->setCodServidor( $inCodServidor );

            if ( !$obErro->ocorreu() ) {
                $obTPessoalServidor->setDado("cod_servidor"           , $this->getCodServidor()                   );
                $obTPessoalServidor->setDado("cod_raca"               , $this->getCodRaca()                       );
                $obTPessoalServidor->setDado("cod_estado_civil"       , $this->getCodEstadoCivil()                );
                $obTPessoalServidor->setDado("numcgm"                 , $this->obRCGMPessoaFisica->getNumCgm()    );
                $obTPessoalServidor->setDado("cod_uf"                 , $this->getCodUF()                         );
                $obTPessoalServidor->setDado("cod_municipio"          , $this->getCodMunicipio()                  );
                $obTPessoalServidor->setDado("cod_edital"             , $this->getCodEdital()                     );
                $obTPessoalServidor->setDado("nr_titulo_eleitor"      , $this->getNrTituloEleitor()               );
                $obTPessoalServidor->setDado("zona_titulo"            , $this->getZonaTitulo()                    );
                $obTPessoalServidor->setDado("secao_titulo"           , $this->getSecaoTitulo()                   );
                $obTPessoalServidor->setDado("caminho_foto"           , $this->getCaminhoFoto()                   );
                $obTPessoalServidor->setDado("nome_mae"               , $this->getNomeMae()                       );
                $obTPessoalServidor->setDado("nome_pai"               , $this->getNomePai()                       );

                $obErro = $obTPessoalServidor->inclusao( $boTransacao );

                //inclui dados para reservista
                if ( !$obErro->ocorreu() ) {
                    $obTPessoalServidorReservista->setDado("cod_servidor"     ,$this->getCodServidor()            );
                    $obTPessoalServidorReservista->setDado("nr_carteira_res"  ,$this->getCarteiraReservista()     );
                    $obTPessoalServidorReservista->setDado("cat_reservista"   ,$this->getCategoriaReservista()    );
                    $obTPessoalServidorReservista->setDado("origem_reservista",$this->getOrigemReservista()       );

                    $obErro = $obTPessoalServidorReservista->inclusao( $boTransacao );
                }

                //inclui dados para PisPasep
                if ( !$obErro->ocorreu() ) {
                    $obTPessoalServidorPisPasep->setDado("cod_servidor"       ,$this->getCodServidor()    );
                    //$obTPessoalServidorPisPasep->setDado("servidor_pis_pasep" ,$this->getPisPasep()       );
                    $obTPessoalServidorPisPasep->setDado("dt_pis_pasep"       ,$this->getDataPisPasep()   );

                    $obErro = $obTPessoalServidorPisPasep->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() and $this->getPisPasep() != "") {
                    $obTCGMPessoaFisica->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCgm());

                    $obErro = $obTCGMPessoaFisica->consultar($boTransacao);

                    if ( !$obErro->ocorreu() ) {
                        $obTCGMPessoaFisica->setDado("servidor_pis_pasep",$this->getPisPasep());

                        $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
                    }
                }

                //inclui dados para Estado Civil
                if ( !$obErro->ocorreu() and $this->obRCGMPessoaFisicaConjuge->getNumCgm() != "" ) {
                    $obTPessoalServidorConjuge->setDado("cod_servidor"            ,$this->getCodServidor()    );
                    $obTPessoalServidorConjuge->setDado("numcgm"                  ,$this->obRCGMPessoaFisicaConjuge->getNumCgm()    );

                    $obErro = $obTPessoalServidorConjuge->inclusao( $boTransacao );
                }

                // inclui dados CID servidor
                if ( !$obErro->ocorreu() ) {
                    if ( $this->obRPessoalCID->getCodCid() != "") {
                        $obTPessoalServidorCid->setDado("cod_servidor" , $this->getCodServidor() );
                        $obTPessoalServidorCid->setDado("cod_cid"      , $this->obRPessoalCID->getCodCID());
                        $obTPessoalServidorCid->setDado("data_laudo", $this->getDataLaudo());
                        $stFiltro = " WHERE cod_servidor = ".$this->getCodServidor()." and cod_cid = ".$this->obRPessoalCID->getCodCid()."";
                        $obErro = $obTPessoalServidorCid->recuperaCid( $rsCid, $stFiltro, "",$boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            if ( $rsCid->getCampo( "cod_cid") != $this->obRPessoalCID->getCodCID() ) {
                                $obErro = $obTPessoalServidorCid->inclusao( $boTransacao );
                            }
                        }
                    }
                }

                // inclui dados CTPS servidor
                if ( !$obErro->ocorreu() ) {
                    for ($inIndex=0;$inIndex<count($this->arRPessoalCTPS);$inIndex++) {
                        $obRPessoalCTPS = &$this->arRPessoalCTPS[$inIndex];

                        $obErro = $obRPessoalCTPS->incluirCTPS( $boTransacao );

                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }

                // inclui dados servidor dependente
                if ( !$obErro->ocorreu() ) {
                    for ($inIndex=0;$inIndex<count($this->arRPessoalDependente);$inIndex++) {
                        $obRPessoalDependente = &$this->arRPessoalDependente[$inIndex];

                        $obErro = $obRPessoalDependente->incluirDependente( $boTransacao );

                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }

                // inclui dados servidor contrato
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->roUltimoContratoServidor->incluirContrato( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->roUltimoContratoServidor->getCodContrato() ) {
                            $obTPessoalServidorContratoServidor->setDado( "cod_servidor", $this->getCodServidor() );
                            $obTPessoalServidorContratoServidor->setDado( "cod_contrato", $this->roUltimoContratoServidor->getCodContrato() );

                            $obErro = $obTPessoalServidorContratoServidor->inclusao( $boTransacao );
                        }
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->roUltimoContratoServidor->gerarAssentamento( $boTransacao );
                    }
                }

                //Inclui arquivos digitais
                if (!$obErro->ocorreu()){
                    $arArquivosDocumentos = $this->getArquivosDocumentos();

                    if(is_array($arArquivosDocumentos) && count($arArquivosDocumentos)>0){
                        $stDirANEXO = CAM_GRH_PESSOAL."anexos/";

                        if (!is_writable($stDirANEXO)) {
                            $obErro->setDescricao(" O diretório ".CAM_GRH_PESSOAL."anexos não possui permissão de escrita!");
                        }

                        if (!$obErro->ocorreu()){
                            $obTPessoalServidorDocumentoDigital = new TPessoalServidorDocumentoDigital();
                            $obTPessoalServidorDocumentoDigital->setDado('cod_servidor', $this->getCodServidor());

                            foreach($arArquivosDocumentos AS $chave => $arquivo){
                                if($arquivo['boCopiado'] == 'FALSE'){
                                    if(!copy($arquivo['tmp_name'],$stDirANEXO.$arquivo['arquivo_digital'])){
                                        $obErro->setDescricao("Erro no upload do arquivo(".$arquivo['name'].")!");
                                        break;
                                    }
                                }

                                if($arquivo['boExcluido']=='FALSE'){
                                    $obTPessoalServidorDocumentoDigital->setDado('cod_tipo'       , $arquivo['inTipoDocDigital']);
                                    $obTPessoalServidorDocumentoDigital->setDado('nome_arquivo'   , $arquivo['name']);
                                    $obTPessoalServidorDocumentoDigital->setDado('arquivo_digital', $arquivo['arquivo_digital']);

                                    $obErro = $obTPessoalServidorDocumentoDigital->inclusao($boTransacao);
                                }else{
                                    $stArquivo = $arquivo['stArquivo'];
                                    if (file_exists($stArquivo)) {
                                        if(!unlink($stArquivo)){
                                            $obErro->setDescricao("Erro ao excluir o arquivo(".$arquivo['name'].")!");
                                        }
                                    }
                                }

                                if ($obErro->ocorreu())
                                    break;
                            }
                        }

                        //Limpar diretório TPM
                        if (!$obErro->ocorreu()){
                            $stDirTMP = CAM_GRH_PESSOAL."tmp/";
                            $obIterator = new DirectoryIterator($stDirTMP);
                            foreach ( $obIterator as $file ) {
                                $stFile = $file->getFilename();
                                if ($stFile!="index.php" && $stFile!="." && $stFile!="..") {
                                    if (file_exists($stDirTMP.$stFile)) {
                                        unlink($stDirTMP.$stFile);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalServidor );

        return $obErro;
    }

   /**
    * Altera Servidor
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o mâtodo
    *
    **/
    public function alterarServidor($boTransacao = "")
    {
        $obErro = new Erro;
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorReservista.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorPisPasep.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorConjuge.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorCid.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorContratoServidor.class.php";
        include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDocumentoDigital.class.php";

        $obTPessoalServidor                 = new TPessoalServidor;
        $obTPessoalServidorReservista       = new TPessoalServidorReservista;
        $obTPessoalServidorPisPasep         = new TPessoalServidorPisPasep;
        $obTPessoalServidorConjuge          = new TPessoalServidorConjuge;
        $obTPessoalServidorCid              = new TPessoalServidorCid;
        $obTPessoalServidorContratoServidor = new TPessoalServidorContratoServidor;
        $obTCGMPessoaFisica                 = new TCGMPessoaFisica;

        if ( !$obErro->ocorreu() and ($this->getCarteiraReservista() or $this->getCategoriaReservista() or $this->getOrigemReservista()) ) {
            if ( $this->getCarteiraReservista() == "" ) {
                $obErro->setDescricao("Campo Certificado de Reservista da guia Documentação inválido!()");
            }
            if ( $this->getCategoriaReservista() == "" ) {
                $obErro->setDescricao("Campo Categoria do Certificado da guia Documentação inválido!()");
            }
            if ( $this->getOrigemReservista() == "") {
                $obErro->setDescricao("Campo Órgão Expedidor do Certificado da guia Documentação inválido!()");
            }
        }

        if ( !$obErro->ocorreu() and $this->getDataNascimento() ) {
            $stFiltro = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCgm();
            $obErro = $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);

            if ( !$obErro->ocorreu() ){
                $obTCGMPessoaFisica->setDado("dt_nascimento",       $this->getDataNascimento());
                $obTCGMPessoaFisica->setDado("numcgm",              $this->obRCGMPessoaFisica->getNumCgm());
                $obTCGMPessoaFisica->setDado("cod_categoria_cnh",   $rsCGM->getCampo('cod_categoria_cnh'));
                $obTCGMPessoaFisica->setDado("orgao_emissor",       $rsCGM->getCampo('orgao_emissor'));
                $obTCGMPessoaFisica->setDado("cpf",                 $rsCGM->getCampo('cpf'));
                $obTCGMPessoaFisica->setDado("num_cnh",             $rsCGM->getCampo('num_cnh'));
                $obTCGMPessoaFisica->setDado("cod_nacionalidade",   $rsCGM->getCampo('cod_nacionalidade'));
                $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
            }
        }

        if ( !$obErro->ocorreu() and $this->obRCGMPessoaFisica->getCPF() ) {
            $inCPF = str_replace(".","", $this->obRCGMPessoaFisica->getCPF());
            $inCPF = str_replace("-","", $inCPF);
            $stFiltro = " WHERE cpf    = '".$inCPF."'";
            $obErro = $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);
            if ( !$obErro->ocorreu() ){
                if ( $rsCGM->getNumLinhas() > 0 ) {
                    $obErro->setDescricao("CPF já cadastrado para outro servidor.");
                } else {
                    $stFiltro  = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCgm();
                    $obErro = $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);
                    if ( !$obErro->ocorreu() ){
                        $obTCGMPessoaFisica->setDado("dt_nascimento",       $rsCGM->getCampo('dt_nascimento'));
                        $obTCGMPessoaFisica->setDado("numcgm",              $this->obRCGMPessoaFisica->getNumCgm());
                        $obTCGMPessoaFisica->setDado("cod_categoria_cnh",   $rsCGM->getCampo('cod_categoria_cnh'));
                        $obTCGMPessoaFisica->setDado("orgao_emissor",       $rsCGM->getCampo('orgao_emissor'));
                        $obTCGMPessoaFisica->setDado("cpf",                 $inCPF);
                        $obTCGMPessoaFisica->setDado("num_cnh",             $rsCGM->getCampo('num_cnh'));
                        $obTCGMPessoaFisica->setDado("cod_nacionalidade",   $rsCGM->getCampo('cod_nacionalidade'));
                        $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obTPessoalServidor->setDado("cod_servidor"           , $this->getCodServidor()                   );
            $obTPessoalServidor->setDado("cod_raca"               , $this->getCodRaca()                       );
            $obTPessoalServidor->setDado("cod_estado_civil"       , $this->getCodEstadoCivil()                );
            $obTPessoalServidor->setDado("numcgm"                 , $this->obRCGMPessoaFisica->getNumCgm()    );
            $obTPessoalServidor->setDado("cod_uf"                 , $this->getCodUF()                         );
            $obTPessoalServidor->setDado("cod_municipio"          , $this->getCodMunicipio()                  );
            $obTPessoalServidor->setDado("cod_edital"             , $this->getCodEdital()                     );
            $obTPessoalServidor->setDado("nr_titulo_eleitor"      , $this->getNrTituloEleitor()               );
            $obTPessoalServidor->setDado("zona_titulo"            , $this->getZonaTitulo()                    );
            $obTPessoalServidor->setDado("secao_titulo"           , $this->getSecaoTitulo()                   );
            $obTPessoalServidor->setDado("caminho_foto"           , $this->getCaminhoFoto()                   );
            $obTPessoalServidor->setDado("nome_mae"               , $this->getNomeMae()                       );
            $obTPessoalServidor->setDado("nome_pai"               , $this->getNomePai()                       );
            $obErro = $obTPessoalServidor->alteracao($boTransacao );
        }
        //altera dados para reservista
        if ( !$obErro->ocorreu() ) {
              $stFiltro  = " WHERE cod_servidor = ".$_POST['inCodServidor'];
              $obErro = $obTPessoalServidorReservista->recuperaTodos($rsResultado, $stFiltro,"",$boTransacao);
              if ( !$obErro->ocorreu() ) {
                $obTPessoalServidorReservista->setDado("cod_servidor"     ,$this->getCodServidor()            );
                $obTPessoalServidorReservista->setDado("nr_carteira_res"  ,$this->getCarteiraReservista()     );
                $obTPessoalServidorReservista->setDado("cat_reservista"   ,$this->getCategoriaReservista()    );
                $obTPessoalServidorReservista->setDado("origem_reservista",$this->getOrigemReservista()       );
                if (!$rsResultado->eof()) {
                    $obErro = $obTPessoalServidorReservista->alteracao( $boTransacao );
                } else {
                    $obErro = $obTPessoalServidorReservista->inclusao( $boTransacao );
                }
              }
        }
        //inclui dados para PisPasep
        if ( !$obErro->ocorreu() ) {
            $obTPessoalServidorPisPasep->setDado("cod_servidor"       ,$this->getCodServidor()    );
            //$obTPessoalServidorPisPasep->setDado("servidor_pis_pasep" ,$this->getPisPasep()       );
            $obTPessoalServidorPisPasep->setDado("dt_pis_pasep"       ,$this->getDataPisPasep()   );
            $obErro = $obTPessoalServidorPisPasep->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() and $this->getPisPasep() != "") {
            $obTCGMPessoaFisica->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCgm());
            $obErro = $obTCGMPessoaFisica->consultar($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $obTCGMPessoaFisica->setDado("servidor_pis_pasep",$this->getPisPasep());
                $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
            }
        }
        //inclui dados para Estado Civil
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRCGMPessoaFisicaConjuge->getNumCgm() != "" ) {
                $obTPessoalServidorConjuge->setDado("cod_servidor"            ,$this->getCodServidor()    );
                $obTPessoalServidorConjuge->setDado("numcgm"                  ,$this->obRCGMPessoaFisicaConjuge->getNumCgm()    );
                $obErro = $obTPessoalServidorConjuge->inclusao( $boTransacao );
            } else {
                $stFiltro = " AND servidor_conjuge.cod_servidor = ".$this->getCodServidor();
                $obErro = $obTPessoalServidorConjuge->recuperaConjuge($rsConjuge,$stFiltro,"",$boTransacao);
                if ( !$obErro->ocorreu() ) {
                    if ( $rsConjuge->getNumLinhas() > 0 ) {
                        $obTPessoalServidorConjuge->setDado("cod_servidor"            ,$rsConjuge->getCampo("cod_servidor")    );
                        $obTPessoalServidorConjuge->setDado("numcgm"                  ,$rsConjuge->getCampo("numcgm")          );
                        $obTPessoalServidorConjuge->setDado("bo_excluido"             ,true);
                        $obErro = $obTPessoalServidorConjuge->inclusao( $boTransacao );
                    }
                }
            }
        }
        // inclui dados CID servidor
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRPessoalCID->getCodCid() != "") {
                $obTPessoalServidorCid->setDado("cod_servidor" , $this->getCodServidor() );
                $obTPessoalServidorCid->setDado("cod_cid"      , $this->obRPessoalCID->getCodCID());
                $obTPessoalServidorCid->setDado("data_laudo", $this->getDataLaudo());
                $obErro = $obTPessoalServidorCid->inclusao( $boTransacao );
            //Se nenhum CID for imformado ele insere com o Câdigo zero (nâo informado)
            } else {
                $obTPessoalServidorCid->setDado("cod_servidor" , $this->getCodServidor() );
                $obTPessoalServidorCid->setDado("cod_cid"      , "0");
                $obErro = $obTPessoalServidorCid->inclusao( $boTransacao );
            }
        }
        // alterar dados CTPS servidor
        if ( !$obErro->ocorreu() ) {
            $this->addRPessoalCTPS();
            $obErro = $this->roRPessoalCTPS->listarCTPS($rsCTPS,$boTransacao);
            if ( !$obErro->ocorreu() ) {
                $arCodCTPS = array();
                while (!$rsCTPS->eof()) {
                    $arCodCTPS[] = $rsCTPS->getCampo("cod_ctps");
                    $rsCTPS->proximo();
                }
                $arCodCTPSEditar = array();
                for ($inIndex=0;$inIndex<count($this->arRPessoalCTPS);$inIndex++) {
                    $obRPessoalCTPS = &$this->arRPessoalCTPS[$inIndex];
                    $arCodCTPSEditar[] = $obRPessoalCTPS->getCodCTPS();
                }
                foreach ($arCodCTPS as $inIndex=>$inCodCTPS) {
                    if ( !in_array($inCodCTPS,$arCodCTPSEditar) ) {
                        $obRPessoalCTPS->setCodCTPS($inCodCTPS);
                        $obErro = $obRPessoalCTPS->excluirCTPS($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
            array_pop($this->arRPessoalCTPS);
        }
        if ( !$obErro->ocorreu() ) {
            for ($inIndex=0;$inIndex<count($this->arRPessoalCTPS);$inIndex++) {
                $obRPessoalCTPS = &$this->arRPessoalCTPS[$inIndex];
                if ( $obRPessoalCTPS->getCodCTPS() ) {
                    $obErro = $obRPessoalCTPS->alterarCTPS( $boTransacao );
                } else {
                    $obErro = $obRPessoalCTPS->incluirCTPS( $boTransacao );
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        // alterar dados servidor dependente
        if ( !$obErro->ocorreu() ) {
            $this->addRPessoalDependente();
            $obErro = $this->roRPessoalDependente->listarPessoalDependente($rsDependente,"",$boTransacao);
            if ( !$obErro->ocorreu() ) {
                $arCodDependente = array();
                while (!$rsDependente->eof()) {
                    $arCodDependente[] = $rsDependente->getCampo('cod_dependente');
                    $rsDependente->proximo();
                }
                $arCodDependentesEditar = array();
                for ($inIndex=0;$inIndex<count($this->arRPessoalDependente);$inIndex++) {
                    $obRPessoalDependente = &$this->arRPessoalDependente[$inIndex];
                    $arCodDependentesEditar[] = $obRPessoalDependente->getCodDependente();
                }
                foreach ($arCodDependente as $inIndex=>$inCodDependente) {
                    if ( !in_array($inCodDependente,$arCodDependentesEditar) ) {
                        $obRPessoalDependente->setCodDependente($inCodDependente);
                        $obErro = $obRPessoalDependente->incluirDependenteExcluido($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
            array_pop($this->arRPessoalDependente);
        }
        if ( !$obErro->ocorreu() ) {
            for ($inIndex=0;$inIndex<count($this->arRPessoalDependente);$inIndex++) {
                $obRPessoalDependente = &$this->arRPessoalDependente[$inIndex];

                $obRPessoalDependente->obRPessoalCID->setCodCid($obRPessoalDependente->obRPessoalCID->getCodCid());

                if ( $obRPessoalDependente->getCodDependente() ) {
                    $obErro = $obRPessoalDependente->alterarDependente( $boTransacao );
                } else {
                    $obErro = $obRPessoalDependente->incluirDependente( $boTransacao );
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
        // inclui dados servidor contrato
        if ( !$obErro->ocorreu() ) {
            if ( $this->roUltimoContratoServidor->getCodContrato() == "" ) {
                $obErro = $this->roUltimoContratoServidor->incluirContrato( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ( $this->roUltimoContratoServidor->getCodContrato() ) {
                        $obTPessoalServidorContratoServidor->setDado( "cod_servidor", $this->getCodServidor() );
                        $obTPessoalServidorContratoServidor->setDado( "cod_contrato", $this->roUltimoContratoServidor->getCodContrato() );
                        $obErro = $obTPessoalServidorContratoServidor->inclusao( $boTransacao );
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->roUltimoContratoServidor->gerarAssentamento( $boTransacao );
                }
            } else {
                $obErro = $this->roUltimoContratoServidor->alterarContrato( $boTransacao );
            }
        }
        //Inclui/Altera arquivos digitais
        if (!$obErro->ocorreu()){
            $arArquivosDocumentos = $this->getArquivosDocumentos();

            if(is_array($arArquivosDocumentos) && count($arArquivosDocumentos)>0){
                $stDirANEXO = CAM_GRH_PESSOAL."anexos/";

                if (!is_writable($stDirANEXO)) {
                    $obErro->setDescricao(" O diretório ".CAM_GRH_PESSOAL."anexos não possui permissão de escrita!");
                }

                if (!$obErro->ocorreu()){
                    $obTPessoalServidorDocumentoDigital = new TPessoalServidorDocumentoDigital();
                    $obTPessoalServidorDocumentoDigital->setDado('cod_servidor', $this->getCodServidor());

                    $obErro = $obTPessoalServidorDocumentoDigital->exclusao($boTransacao);

                    if (!$obErro->ocorreu()){
                        foreach($arArquivosDocumentos AS $chave => $arquivo){
                            if($arquivo['boCopiado'] == 'FALSE'){
                                if(!copy($arquivo['tmp_name'],$stDirANEXO.$arquivo['arquivo_digital'])){
                                    $obErro->setDescricao("Erro no upload do arquivo(".$arquivo['name'].")!");
                                    break;
                                }
                            }

                            if($arquivo['boExcluido']=='FALSE'){
                                $obTPessoalServidorDocumentoDigital->setDado('cod_tipo'       , $arquivo['inTipoDocDigital']);
                                $obTPessoalServidorDocumentoDigital->setDado('nome_arquivo'   , $arquivo['name']);
                                $obTPessoalServidorDocumentoDigital->setDado('arquivo_digital', $arquivo['arquivo_digital']);

                                $obErro = $obTPessoalServidorDocumentoDigital->inclusao($boTransacao);
                            }else{
                                $stArquivo = $arquivo['stArquivo'];
                                if (file_exists($stArquivo)) {
                                    if(!unlink($stArquivo)){
                                        $obErro->setDescricao("Erro ao excluir o arquivo(".$arquivo['name'].")!");
                                    }
                                }
                            }

                            if ($obErro->ocorreu())
                                break;
                        }
                    }
                }

                //Limpar diretório TPM
                if (!$obErro->ocorreu()){
                    $stDirTMP = CAM_GRH_PESSOAL."tmp/";
                    $obIterator = new DirectoryIterator($stDirTMP);
                    foreach ( $obIterator as $file ) {
                        $stFile = $file->getFilename();
                        if ($stFile!="index.php" && $stFile!="." && $stFile!="..") {
                            if (file_exists($stDirTMP.$stFile)) {
                                unlink($stDirTMP.$stFile);
                            }
                        }
                    }
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalServidor );

        return $obErro;
    }

    /**
    * Exclui empresa que fornece vale-transportes
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o mâtodo
    */
    public function excluirServidor($boTransacao = "")
    {
        $obErro = new Erro;
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorReservista.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorPisPasep.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorConjuge.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorCid.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorContratoServidor.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorContaSalarioHistorico.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoNorma.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoExcluido.class.php";        
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php";
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorComplementar.class.php";
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php";
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoIRRF.class.php";
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidencia.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php";
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDocumentoDigital.class.php";

        //Inicio da verificações da exclusão do servidor
        $stMensagem = "Exclusão não permitida, servidor possui histórico de dados no sistema.";
        $stFiltro = " WHERE cod_contrato = ".$this->roUltimoContratoServidor->getCodContrato();

        $obTPessoalAssentamentoGeradoNorma = new TPessoalAssentamentoGeradoNorma();
        $obErro = $obTPessoalAssentamentoGeradoNorma->excluirAssentamentoGeradoNorma($stFiltro, $boTransacao);

        if ( !$obErro->ocorreu() ) {
            $obTPessoalAssentamentoGeradoExcluido = new TPessoalAssentamentoGeradoExcluido();
            $obErro = $obTPessoalAssentamentoGeradoExcluido->excluirAssentamentoGeradoExcluido($stFiltro, $boTransacao);

            if ( !$obErro->ocorreu() ) {
                $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGerado();
                $obErro = $obTPessoalAssentamentoGeradoContratoServidor->excluirAssentamentoGerado($stFiltro, $boTransacao);

                if ( !$obErro->ocorreu() ) {
                    $obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor;
                    $obErro = $obTPessoalAssentamentoGeradoContratoServidor->excluirAssentamentoGeradoContratoServidor($stFiltro, $boTransacao);

                    if ( !$obErro->ocorreu() ) {
                        $obTPessoalContratoServidorContaSalarioHistorico = new TPessoalContratoServidorContaSalarioHistorico;
                        $obErro = $obTPessoalContratoServidorContaSalarioHistorico->excluirContratoServidorContaSalarioHistorico($stFiltro, $boTransacao);

                        if ( !$obErro->ocorreu() ) {
                            $arTabelasVerificacao = array("Folha Salário"                   => "TFolhaPagamentoContratoServidorPeriodo",
                                                          "Adido Cedido"                    => "TPessoalAdidoCedido",
                                                          "Aposentadoria"                   => "TPessoalAposentadoria",
                                                          "Assentamento Gerado"             => "TPessoalAssentamentoGeradoContratoServidor",
                                                          "Caso Causa"                      => "TPessoalContratoServidorCasoCausa",
                                                          "Cedencia"                        => "TPessoalContratoServidorCedencia",
                                                          "Férias"                          => "TPessoalFerias",
                                                          "Pensionista"                     => "TPessoalContratoPensionista",
                                                          "Concessão de Décimo"             => "TFolhaPagamentoConcessaoDecimo",
                                                          "Complementar"                    => "TFolhaPagamentoContratoServidorComplementar",
                                                          "Desconto Externo IRRF"           => "TFolhaPagamentoDescontoExternoIRRF",
                                                          "Desconto Externo Previdência"    => "TFolhaPagamentoDescontoExternoPrevidencia",
                                                          "Concessão de Vale-Transporte"    => "TBeneficioContratoServidorConcessaoValeTransporte",
                                                          "Concessão de Vale-Transporte"    => "TBeneficioContratoServidorGrupoConcessaoValeTransporte",);

                            foreach ($arTabelasVerificacao as $stDescricaoTabela => $stTabela) {
                                if ( !$obErro->ocorreu() ) {
                                    if (strpos($stTabela,"TPessoal") === 0) {
                                        include_once(CAM_GRH_PES_MAPEAMENTO.$stTabela.".class.php");
                                    }
                                    if (strpos($stTabela,"TBeneficio") === 0) {
                                        include_once(CAM_GRH_BEN_MAPEAMENTO.$stTabela.".class.php");
                                    }
                                    if (strpos($stTabela,"TFolhaPagamento") === 0) {
                                        include_once(CAM_GRH_FOL_MAPEAMENTO.$stTabela.".class.php");
                                    }
                                    $obTVerificacaoExclusao = new $stTabela;
                                    $obErro = $obTVerificacaoExclusao->recuperaTodos($rsVerificacao,$stFiltro,"",$boTransacao);

                                    if ( !$obErro->ocorreu() ){                                        
                                        if ($rsVerificacao->getNumLinhas() > 0)
                                            if ($stTabela != 'TFolhaPagamentoContratoServidorPeriodo') {
                                                $obErro->setDescricao($stMensagem."(".$stDescricaoTabela.")");                                            
                                            }
                                    }

                                    if ( $obErro->ocorreu() )
                                        break;
                                }
                            }
                            //Fim da verificação da exclusão do servidor

                            $obTPessoalServidor                 = new TPessoalServidor;
                            $obTPessoalServidorReservista       = new TPessoalServidorReservista;
                            $obTPessoalServidorPisPasep         = new TPessoalServidorPisPasep;
                            $obTPessoalServidorConjuge          = new TPessoalServidorConjuge;
                            $obTPessoalServidorCid              = new TPessoalServidorCid;
                            $obTPessoalServidorContratoServidor = new TPessoalServidorContratoServidor;
                            if ( !$obErro->ocorreu() ) {
                                $stFiltro = " WHERE cod_servidor = ".$this->getCodServidor();
                                $obErro = $obTPessoalServidorContratoServidor->recuperaTodos( $rsServidorContratoServidor,$stFiltro,"",$boTransacao );
                            }
                            if ( !$obErro->ocorreu() ) {
                                $obErro = $this->roUltimoContratoServidor->excluirContratoServidor( $boTransacao );
                            }
                            //excluir dados para reservista
                            if ( !$obErro->ocorreu() ) {
                                $obTPessoalServidorReservista->setDado("cod_servidor"     ,$this->getCodServidor()            );
                                $obErro = $obTPessoalServidorReservista->exclusao( $boTransacao );
                            }
                            //excluir dados para PisPasep
                            if ( !$obErro->ocorreu() ) {
                                $obTPessoalServidorPisPasep->setDado("cod_servidor"       ,$this->getCodServidor()    );
                                $obErro = $obTPessoalServidorPisPasep->exclusao( $boTransacao );
                            }
                            //excluir dados para Estado Civil
                            if ( !$obErro->ocorreu() ) {
                                $obTPessoalServidorConjuge->setDado("cod_servidor"            ,$this->getCodServidor()    );
                                $obErro = $obTPessoalServidorConjuge->exclusao( $boTransacao );
                            }
                            // exclui dados CID servidor
                            if ( !$obErro->ocorreu() and $rsServidorContratoServidor->getNumLinhas() == 1 ) {
                                $obTPessoalServidorCid->setDado("cod_servidor" , $this->getCodServidor() );
                                $obErro = $obTPessoalServidorCid->exclusao( $boTransacao );
                            }
                            // exclui dados CTPS servidor
                            if ( !$obErro->ocorreu() and $rsServidorContratoServidor->getNumLinhas() == 1 ) {
                                $obErro = $this->roRPessoalCTPS->listarCTPS($rsCTPS,$boTransacao);
                                if ( !$obErro->ocorreu() ) {
                                    while ( !$rsCTPS->eof() ) {
                                        $this->roRPessoalCTPS->setCodCTPS($rsCTPS->getCampo('cod_ctps'));
                                        $obErro = $this->roRPessoalCTPS->excluirCTPS( $boTransacao );
                                        if ( $obErro->ocorreu() ) {
                                            break;
                                        }
                                        $rsCTPS->proximo();
                                    }
                                }
                            }
                            // exclui dados servidor dependente
                            if ( !$obErro->ocorreu() and $rsServidorContratoServidor->getNumLinhas() == 1 ) {
                                $obErro = $this->roRPessoalDependente->listarPessoalDependente($rsDependentes,"",$boTransacao);
                                if ( !$obErro->ocorreu() ) {
                                    while ( !$rsDependentes->eof() ) {
                                        $this->roRPessoalDependente->setCodDependente( $rsDependentes->getCampo('cod_dependente') );
                                        $obErro = $this->roRPessoalDependente->excluirDependente( $boTransacao );
                                        if ( $obErro->ocorreu() ) {
                                            break;
                                        }
                                        $rsDependentes->proximo();
                                    }
                                }
                            }
                            // exclui dados servidor contrato
                            if ( !$obErro->ocorreu() and $rsServidorContratoServidor->getNumLinhas() == 1 ) {
                                $obTPessoalServidorContratoServidor->setDado( "cod_servidor", $this->getCodServidor() );
                                if ( !$obErro->ocorreu() ) {
                                    $obErro = $obTPessoalServidorContratoServidor->exclusao( $boTransacao );
                                }
                            }

                            // exclui arquivos digitais
                            if ( !$obErro->ocorreu() ){
                                $obTPessoalServidorDocumentoDigital = new TPessoalServidorDocumentoDigital();
                                $obTPessoalServidorDocumentoDigital->setDado('cod_servidor', $this->getCodServidor());
                                $obErro = $obTPessoalServidorDocumentoDigital->recuperaServidorDocumentoDigital($rsServidorArqDigitais, "", "", $boTransacao);

                                if ( !$obErro->ocorreu() ){
                                    $stDirANEXO = CAM_GRH_PESSOAL."anexos/";

                                    if (!is_writable($stDirANEXO)) {
                                        $obErro->setDescricao(" O diretório ".CAM_GRH_PESSOAL."anexos não possui permissão de escrita!");
                                    }

                                    if (!$obErro->ocorreu()){
                                        foreach($rsServidorArqDigitais->getElementos() AS $chave => $arqDigital){
                                            if (file_exists($stDirANEXO.$arqDigital['arquivo_digital'])) {
                                                if(!unlink($stDirANEXO.$arqDigital['arquivo_digital'])){
                                                    $obErro->setDescricao("Erro ao excluir o arquivo(".$arquivo['name'].")!");
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                if ( !$obErro->ocorreu() ){
                                    $obErro = $obTPessoalServidorDocumentoDigital->exclusao($boTransacao);
                                }

                                //Limpar diretório TPM
                                if (!$obErro->ocorreu()){
                                    $stDirTMP = CAM_GRH_PESSOAL."tmp/";
                                    $obIterator = new DirectoryIterator($stDirTMP);
                                    foreach ( $obIterator as $file ) {
                                        $stFile = $file->getFilename();
                                        if ($stFile!="index.php" && $stFile!="." && $stFile!="..") {
                                            if (file_exists($stDirTMP.$stFile)) {
                                                unlink($stDirTMP.$stFile);
                                            }
                                        }
                                    }
                                }
                            }

                            if ( !$obErro->ocorreu() and $rsServidorContratoServidor->getNumLinhas() == 1 ) {
                                $obTPessoalServidor->setDado("cod_servidor",$this->getCodServidor());
                                $obErro = $obTPessoalServidor->exclusao($boTransacao);
                            }
                        }
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalServidor );

        return $obErro;
    }

    /**
        * Adiciona um dependente
        * @access Public
    */
    public function addRPessoalDependente()
    {
        $this->arRPessoalDependente[] = new RPessoalDependente ( $this );
        $this->roRPessoalDependente   = &$this->arRPessoalDependente[ count($this->arRPessoalDependente) - 1 ];
    }

    /**f
    * Executa um recuperaTodos na classe Persistente EmpresaTransporte
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsPessoalServidor, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obTPessoalServidor = new TPessoalServidor;
        $obErro = $obTPessoalServidor->recuperaRelacionamento( $rsPessoalServidor , "" ,"" ,$boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos para relatório na classe Persistente PessoalServidor
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarRelatorio(&$rsPessoalServidor, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obTPessoalServidor = new TPessoalServidor;
        $obErro = $obTPessoalServidor->recuperaRelacionamentoRelatorio( $rsPessoalServidor , $stFiltro , $stOrder,$boTransacao );
        return $obErro;
    }

    /**
    * Executa um recuperaTodos para relatârio na classe Persistente PessoalServidor
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarRelatorioPorContrato(&$rsPessoalServidor, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obTPessoalServidor = new TPessoalServidor;
        $obErro = $obTPessoalServidor->recuperaRelacionamentoRelatorioPorContrato( $rsPessoalServidor , $stFiltro , $stOrder,$boTransacao );

        return $obErro;
    }

    /**
        * Adiciona um Dependente  ao array de referencia-objeto
        * @access Public
    */
    public function addDependente()
    {
        $this->arRPessoalDependente[] = new RPessoalDependente ( $this );
        $this->roUltimoDependente     = &$this->arRPessoalDependente[ count($this->arRPessoalDependente) - 1 ];
    }
    /**
        * Retira um Dependente  do array de referencia-objeto
        * @access Public
    */
    public function commitDependente()
    {
        $this->arRPessoalDependente = array_pop ($this->arRPessoalDependente);
    }

    /**
        * Adiciona um ContratoServidor ao array de referencia-objeto
        * @access Public
    */
    public function addContratoServidor()
    {
        $this->arRPessoalContratoServidor[] = new RPessoalContratoServidor ( $this );
        $this->roUltimoContratoServidor     = &$this->arRPessoalContratoServidor[ count($this->arRPessoalContratoServidor) - 1 ];
    }
    /**
        * Retira um ContratoServidor do array de referencia-objeto
        * @access Public
    */
    public function commitContratoServidor()
    {
        $this->arRPessoalContratoServidor = array_pop ($this->arRPessoalContratoServidor );
    }

    /**
        * Adiciona um CTPS ao array de referencia-objeto
        * @access Public
    */
    public function addRPessoalCTPS()
    {
        $this->arRPessoalCTPS[] = new RPessoalCTPS ( $this );
        $this->roRPessoalCTPS   = &$this->arRPessoalCTPS[ count($this->arRPessoalCTPS) - 1 ];
    }

    public function consultarServidor(&$rsRecordSet, $boTransacao)
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php"                       );
        $obTPessoalServidor = new TPessoalServidor;
        $stOrdem = "";
        if ( $this->obRCGMPessoaFisica->getNumCGM() ) {
            $stFiltro = " WHERE PS.numcgm =".$this->obRCGMPessoaFisica->getNumCGM();
            $obTPessoalServidor->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        }

        if ( $this->getCodServidor() ) {
            $stFiltro = " WHERE PS.cod_servidor = ". $this->getCodServidor();
            $obTPessoalServidor->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
        }

    }

    // METODOS DE INTERFACE

    /**
    * Executa um recuperaTodos na classe Persistente estado civil
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function recuperaTodosEstadoCivil(&$rsResultado , $boTransacao = "")
    {
        include_once(CAM_GA_CGM_MAPEAMENTO."TEstadoCivil.class.php");
        $obTEstadoCivil = new TEstadoCivil;
        $obErro = $obTEstadoCivil->recuperaTodos( $rsResultado, "", "nom_estado", $boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Raca
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function recuperaTodosRaca(&$rsResultado , $boTransacao = "")
    {
        include_once(CAM_GA_CGM_MAPEAMENTO."TRaca.class.php");
        $obTRaca = new TRaca;
        $stFiltro = "";
        if ( $this->getCodRais() ) {
            $stFiltro  = " WHERE cod_rais = ".$this->getCodRais();
        }
        $obErro = $obTRaca->recuperaTodos( $rsResultado, $stFiltro, "nom_raca", $boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Pais
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function recuperaTodosPais(&$rsResultado , $stFiltro, $boTransacao = "")
    {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php");
        $obTPais = new TPais;
        $obErro = $obTPais->recuperaTodos( $rsResultado, $stFiltro, "nom_pais", $boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente UF
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */

    public function recuperaTodosUF(&$rsResultado , $boTransacao = "")
    {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php");
        $obTUF = new TUF;
        $obErro = $obTUF->recuperaTodos( $rsResultado, "", "nom_uf", $boTransacao );

        return $obErro;
    }

    public function listarUF(&$rsRecordSet,$inCodUF , $boTransacao = "")
    {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoPais.class.php");
        $obTUF = new TUF;
        $stFiltro = " WHERE cod_uf = ".$inCodUF;
        $obErro = $obTUF->recuperaTodos( $rsRecordSet, $stFiltro, "nom_uf", $boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Municipios
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function recuperaTodosMunicipio(&$rsResultado , $inCodUF, $boTransacao = "")
    {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
        $obTMunicipio = new TMunicipio;
        if ($inCodUF) {
            $stFiltro = " WHERE cod_uf = ".$inCodUF;
        }
        $obErro = $obTMunicipio->recuperaTodos( $rsResultado, $stFiltro, "nom_municipio", $boTransacao );

        return $obErro;
    }

    public function listarMunicipio(&$rsRecordSet , $inCodMunicipio, $inCodUF, $boTransacao = "")
    {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
        $obTMunicipio = new TMunicipio;
        $stFiltro  = " WHERE cod_municipio = ". $inCodMunicipio;
        $stFiltro .= "   AND cod_uf        = ". $inCodUF;
        $obErro = $obTMunicipio->recuperaTodos( $rsRecordSet, $stFiltro, "nom_municipio", $boTransacao );

        return $obErro;
    }

    /**
    * Executa uma consulta pelo CGM na classe Persistente Servidor
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultaCGMServidor(&$rsResultado , $stFiltro = "", $boTransacao = "")
    {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obTPessoalServidor = new TPessoalServidor;
        $stFiltro = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCGM();
        $obErro = $obTPessoalServidor->recuperaCGMServidor( $rsResultado, $stFiltro, $boTransacao );

        return $obErro;
    }

    /**
    * Executa uma consulta dos registros pelo CGM na classe Persistente Servidor
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultaRegistrosServidor(&$rsRecordset , $stFiltro = "", $boTransacao = "", $boRescindido = false)
    {
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obTPessoalServidor = new TPessoalServidor;
        $stFiltro .= " AND ps.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM()."   \n";

        if (!$boRescindido) {
            $stFiltro .= " AND pc.cod_contrato NOT IN (                                 \n";
            $stFiltro .= "    SELECT                                                    \n";
            $stFiltro .= "        cod_contrato                                          \n";
            $stFiltro .= "    FROM                                                      \n";
            $stFiltro .= "        pessoal.contrato_servidor_caso_causa ) \n";
        }

        $obErro = $obTPessoalServidor->recuperaRegistrosServidor( $rsRecordset, $stFiltro, $boTransacao );

        return $obErro;
    }

    public function recuperaCgmDoRegistro(&$rsRecordset , $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro  = " AND numcgm = ".$this->obRCGMPessoaFisica->getNumCGM();
        $stFiltro .= " AND situacao = 'Ativo' ";
        $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsRecordset,$stFiltro, $stOrdem,$boTransacao);
        return $obErro;
    }

}

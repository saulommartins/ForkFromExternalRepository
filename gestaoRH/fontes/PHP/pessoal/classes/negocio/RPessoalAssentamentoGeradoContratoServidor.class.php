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
    * Classe de regra de negócio dos assentamentos gerados.
    * Data de Criação: 30/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Regra

    $Id: RPessoalAssentamentoGeradoContratoServidor.class.php 66301 2016-08-05 13:36:34Z michel $

    Caso de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalGeracaoAssentamento.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoArquivoDigital.class.php";

class RPessoalAssentamentoGeradoContratoServidor
{
/**
    * @access Private
    * @var Array
*/
var $arRPessoalGeracaoAssentamento;
/**
    * @access Private
    * @var Object
*/
var $roRPessoalGeracaoAssentamento;
/**
   * @access Private
   * @var Object
*/
var $arArquivosDigitais;

/**
   * @access Private
   * @var Object
*/
var $arArquivosDigitaisExcluir;

/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalAssentamentoGeradoContratoServidor($valor) { $this->arRPessoalGeracaoAssentamento  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalAssentamentoGeradoContratoServidor(&$valor) { $this->roRPessoalGeracaoAssentamento = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setArquivoDigital($valor) { $this->arArquivosDigitais = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setArquivoDigitalExcluir($valor) { $this->arArquivosDigitaisExcluir = $valor; }

/**
    * @access Public
    * @return Array
*/
function getARRPessoalAssentamentoGeradoContratoServidor() { return $this->arRPessoalGeracaoAssentamento;  }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalAssentamentoGeradoContratoServidor() { return $this->roRPessoalGeracaoAssentamento;  }
/**
    * @access Public
    * @return Object
*/
function getArquivoDigital() { return $this->arArquivosDigitais; }
/**
    * @access Public
    * @return Object
*/
function getArquivoDigitalExcluir() { return $this->arArquivosDigitaisExcluir; }

/**
    * Método construtor
    * @access Private
*/
function RPessoalAssentamentoGeradoContratoServidor()
{
    $this->setArquivoDigital(array());
    $this->setArquivoDigitalExcluir(array());
}

/**
    * Adiciona um GeracaoAssentamento ao objeto
    * @access Public
    * @param  Object $obTransacao
*/
function addRPessoalGeracaoAssentamento()
{
    $this->roRPessoalGeracaoAssentamento   = new RPessoalGeracaoAssentamento();
    $this->arRPessoalGeracaoAssentamento[] = $this->roRPessoalGeracaoAssentamento;
}

/**
    * Adiciona um GeracaoAssentamento ao objeto
    * @access Public
    * @param  Object $obTransacao
*/
function addArquivoDigital($arquivo)
{
    $this->arArquivosDigitais[] = $arquivo;
}
/**
    * Adiciona um GeracaoAssentamento ao objeto
    * @access Public
    * @param  Object $obTransacao
*/
function addArquivoDigitalExcluir($arquivo)
{
    $this->arArquivosDigitaisExcluir[] = $arquivo;
}

/**
    * Grava no banco de dados todos os assentamentos gerados
    * @access Public
    * @param  Object $obTransacao
    * @return Object Objeto Erro
*/
function incluirAssentamentoGeradoContratoServidor($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach($this->arRPessoalGeracaoAssentamento AS $chave => $obRPessoalGeracaoAssentamento){
            $obErro = $obRPessoalGeracaoAssentamento->incluirGeracaoAssentamento( $boTransacao );

            if ( $obErro->ocorreu() )
                break;

            $this->roRPessoalGeracaoAssentamento = $obRPessoalGeracaoAssentamento;
            $this->arRPessoalGeracaoAssentamento[$chave] = $this->roRPessoalGeracaoAssentamento;
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalConselho );

    return $obErro;
}

/**
    * Grava/Exclui no banco de dados todos os arquivos dos assentamentos gerados
    * @access Public
    * @param  Object $obTransacao
    * @return Object Objeto Erro
*/
function executaArquivoDigital($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $obTPessoalAssentamentoArquivoDigital = new TPessoalAssentamentoArquivoDigital();

    if ( !$obErro->ocorreu() ) {
        foreach($this->getArquivoDigital() AS $chave => $arquivo){
            $obTPessoalAssentamentoArquivoDigital->setDado('cod_assentamento_gerado' , $arquivo['inCodAssentamentoGerado']);
            $obTPessoalAssentamentoArquivoDigital->setDado('nome_arquivo'            , $arquivo['name']);
            $obTPessoalAssentamentoArquivoDigital->setDado('arquivo_digital'         , $arquivo['arquivo_digital']);

            if( $arquivo['boCopiado'] == 'TRUE' )
                $obErro = $obTPessoalAssentamentoArquivoDigital->alteracao( $boTransacao );
            else{
                $obErro = $obTPessoalAssentamentoArquivoDigital->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    if(!copy($arquivo['tmp_name'], $arquivo['stArquivo']))
                        $obErro->setDescricao("Erro no upload do arquivo(".$arquivo['name'].")!");
                }
            }

            if ( $obErro->ocorreu() )
                break;
        }
    }

    if ( !$obErro->ocorreu() ) {
        foreach($this->getArquivoDigitalExcluir() AS $chave => $arquivo){
            $obTPessoalAssentamentoArquivoDigital->setDado('cod_assentamento_gerado' , $arquivo['inCodAssentamentoGerado']);
            $obTPessoalAssentamentoArquivoDigital->setDado('nome_arquivo'            , $arquivo['name']);
            $obTPessoalAssentamentoArquivoDigital->setDado('arquivo_digital'         , $arquivo['arquivo_digital']);
            $obErro = $obTPessoalAssentamentoArquivoDigital->exclusao($boTransacao);

            if ( $obErro->ocorreu() ) {
                $stArquivo = $arquivo['stArquivo'];
                if (file_exists($stArquivo)) {
                    if(!unlink($stArquivo))
                        $obErro->setDescricao("Erro ao excluir o arquivo(".$arquivo['name'].")!");
                }
            }

            if ( $obErro->ocorreu() )
                break;
        }
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalAssentamentoArquivoDigital );

    return $obErro;
}

/**
    * Limpar arquivos digitais do diretório Pessoal TPM
    * @access Public
*/
function limparDiretorioPessoalTPM()
{
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
?>

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
    * Classe de regra de negócio para desdobramento de receita
    * Data de Criação: 15/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-04-01 09:13:21 -0300 (Ter, 01 Abr 2008) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.8  2007/01/22 18:21:04  cako
Bug #8154#

Revision 1.7  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"        );

/**
* Classe de regra de negócio para desdobramento de receita
* Data de Criação: 15/02/2005

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Cassiano de Vasconcellos Fereira

* @package URBEM
* @subpackage Regra
*/

class RContabilidadeDesdobramentoReceita
{
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $roROrcamentoReceitaPrincipal;
/**
    * @access Private
    * @var Object
*/
var $roUltimaReceitaSecundaria;
/**
    * @access Private
    * @var Array
*/
var $arROrcamentoReceitaSecundaria;

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeDesdobramentoReceita(&$roROrcamentoReceitaPrincipal)
{
    $this->obTransacao = new Transacao;
    $this->roROrcamentoReceitaPrincipal = &$roROrcamentoReceitaPrincipal;
    $this->arROrcamentoReceitaSecundaria = array();
}

/**
    * Salva as confrontações secundárias, incluindo, alterando e excluindo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaReceitaSecundaria( $rsContaSecundaria, $boTransacao );
        if ( !$obErro->ocorreu() and !$rsContaSecundaria->eof() ) {
            $obErro->setDescricao( " Receita secundária não pode ser desdobrada!" );
        } else {
            $nuSomatorioPercentual = 0;//DEVE SER <= 100
            $obErro = $this->listar( $rsListaReceitaSecundaria, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arListaReceitaSecundaria = array();
                while ( !$rsListaReceitaSecundaria->eof() ) {
                    $arListaReceitaSecundaria[ $rsListaReceitaSecundaria->getCampo("cod_receita_secundaria") ] =  $rsListaReceitaSecundaria->getCampo("percentual");
                    $rsListaReceitaSecundaria->proximo();
                }
                $obTContabilidadeDesdobramentoReceita->setDado( "exercicio", $this->roROrcamentoReceitaPrincipal->getExercicio() );
                $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita_principal", $this->roROrcamentoReceitaPrincipal->getCodReceita() );
                foreach ($this->arROrcamentoReceitaSecundaria as $obROrcamentoReceitaSecundaria) {
                    $obErro = $this->verificaReceitaSecundaria2( $rsContaSecundaria, $obROrcamentoReceitaSecundaria->getCodReceita(), $boTransacao );
                    if ( !$obErro->ocorreu() and !$rsContaSecundaria->eof() ) {
                        $obErro->setDescricao( "A receita ".$obROrcamentoReceitaSecundaria->getCodReceita()." já é receita secundária de outra receita!" );
                        break;
                    }
                    $obErro = $this->verificaReceitaSecundaria3( $rsContaSecundaria, $obROrcamentoReceitaSecundaria->getCodReceita(), $boTransacao );
                    if ( !$obErro->ocorreu() and !$rsContaSecundaria->eof() ) {
                        $obErro->setDescricao( "A receita ".$obROrcamentoReceitaSecundaria->getCodReceita()." já é receita principal de outra receita!" );
                        break;
                    }

                    $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita_secundaria", $obROrcamentoReceitaSecundaria->getCodReceita() );
                    $obTContabilidadeDesdobramentoReceita->setDado( "percentual", $obROrcamentoReceitaSecundaria->getPercentualDesdobramento() );
                    $nuPercentual = str_replace( ".", "", $obROrcamentoReceitaSecundaria->getPercentualDesdobramento() );
                    $nuPercentual = str_replace( ",", ".", $nuPercentual );
                    $nuSomatorioPercentual += $nuPercentual;
                    //VERIFICA A ALTERACAO
                    if ( $arListaReceitaSecundaria[$obROrcamentoReceitaSecundaria->getCodReceita()] ) {
                        if ( $arListaReceitaSecundaria[$obROrcamentoReceitaSecundaria->getCodReceita()] != $obROrcamentoReceitaSecundaria->getPercentualDesdobramento() ) {
                            $obErro = $obTContabilidadeDesdobramentoReceita->alteracao( $boTransacao );
                        }
                        unset( $arListaReceitaSecundaria[$obROrcamentoReceitaSecundaria->getCodReceita()] );
                    } else {//EXECUTA A INCLUSAO
                        $obErro = $obTContabilidadeDesdobramentoReceita->inclusao( $boTransacao );
                    }
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
                //VERIFICA O SOMATORIO DO PERCENTUAL
                if ( !$obErro->ocorreu() and $nuSomatorioPercentual > 100.00 ) {
                    $obErro->setDescricao( "A soma do percentual das receitas secundárias deve ser menor ou igual a 100,00." );
                }
                //EXCLUSAO
                if ( !$obErro->ocorreu() ) {
                    foreach ($arListaReceitaSecundaria as $inCodigoReceita => $flPercentual) {
                        $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita_secundaria", $inCodigoReceita );
                        $obErro = $obTContabilidadeDesdobramentoReceita->exclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeDesdobramentoReceita );

    return $obErro;
}

/**
    * Lista as receitas secundarias conforme o filtro informado
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

    $stFiltro = "";
    if ( $this->roROrcamentoReceitaPrincipal->getExercicio() ) {
        $stFiltro .= " exercicio = '".$this->roROrcamentoReceitaPrincipal->getExercicio()."' AND ";
    }
    if ( $this->roROrcamentoReceitaPrincipal->getCodReceita() ) {
        $stFiltro .= "cod_receita_principal = ".$this->roROrcamentoReceitaPrincipal->getCodReceita()." AND ";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $stOrdem = "";
    $obErro = $obTContabilidadeDesdobramentoReceita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Consulta os dados de uma receita especifica
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

    $obErro = $this->roROrcamentoReceitaPrincipal->consultar( $rs, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro .= " WHERE exercicio = '".$this->roROrcamentoReceitaPrincipal->getExercicio()."' AND ";
        $stFiltro .= "cod_receita_principal = ".$this->roROrcamentoReceitaPrincipal->getCodReceita()." ";
        $obErro = $obTContabilidadeDesdobramentoReceita->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            while ( !$rsRecordSet->eof() ) {
                $this->addReceitaSecundaria();
                $this->roUltimaReceitaSecundaria->setCodReceita( $rsRecordSet->getCampo("cod_receita_secundaria") );
                $this->roUltimaReceitaSecundaria->setExercicio ( $rsRecordSet->getCampo("exercicio") );
                $this->roUltimaReceitaSecundaria->setPercentualDesdobramento( $rsRecordSet->getCampo("percentual") );
                $rsRecordSet->proximo();
            }
        }
    }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeDesdobramentoReceita->setDado( "exercicio", $this->roROrcamentoReceitaPrincipal->getExercicio() );
        $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita_principal", $this->roROrcamentoReceitaPrincipal->getCodReceita() );
        $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita_secundaria", $this->roUltimaReceitaSecundaria->getCodReceita() );
        $obErro = $obTContabilidadeDesdobramentoReceita->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeDesdobramentoReceita );

    return $obErro;
}

/**
    * Adiciona um objeto de receita na classe
    * @access Public
*/
function addReceitaSecundaria()
{
    $this->arROrcamentoReceitaSecundaria[] = new ROrcamentoReceita;
    $this->roUltimaReceitaSecundaria = &$this->arROrcamentoReceitaSecundaria[ count( $this->arROrcamentoReceitaSecundaria ) - 1 ];
}

/**
    * Gera um array com as receitas secundarias de uma determinada receita no padrão para gera a interface
    * @access Public
    * @param  Object $arLista Retorna um array preenchido no padrão para gerar a interface
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function geraListaReceitaSecundaria(&$arLista ,$boTransacao = "")
{
    $arLista = array();
    foreach ($this->arROrcamentoReceitaSecundaria as $obROrcamentoReceitaSecundaria) {
       $obErro = $obROrcamentoReceitaSecundaria->listar( $rsLista, $boTransacao );
       if ( !$obErro->ocorreu() ) {
           while ( !$rsLista->eof() ) {
               $stCodigoEstrutural = $rsLista->getCampo( "mascara_classificacao" );
               $stNomeReceita      = $rsLista->getCampo( "descricao" );
               $inCodigoRecurso    = $rsLista->getCampo( "cod_recurso" );
               $stNomeRecurso      = $rsLista->getCampo( "nom_recurso" );
               $nuPercentualDesdobramento = number_format( $obROrcamentoReceitaSecundaria->getPercentualDesdobramento(), 2, ",", ".");
               $arLista[] = array( "cod_receita"    => $obROrcamentoReceitaSecundaria->getCodReceita(),
                                   "cod_estrutural" => $stCodigoEstrutural,
                                   "nom_receita"    => $stNomeReceita,
                                   "cod_recurso"    => $inCodigoRecurso,
                                   "nom_recurso"    => $stNomeRecurso,
                                   "percentual"     => $nuPercentualDesdobramento,
                                   "linha"          => count( $arLista ) + 1
                                 );
               $rsLista->proximo();
           }
       }
    }

    return $obErro;
}

/**
    * Verifica se a receita principal informada é secundaria de alguma outra
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaReceitaSecundaria(&$rsLista,  $boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

     $stFiltro  = " WHERE ";
     $stFiltro .= " exercicio = '".$this->roROrcamentoReceitaPrincipal->getExercicio()."' AND ";
     $stFiltro .= " cod_receita_secundaria = ".$this->roROrcamentoReceitaPrincipal->getCodReceita();
     $obErro = $obTContabilidadeDesdobramentoReceita->recuperaTodos( $rsLista, $stFiltro, "", $boTransacao );

     return $obErro;
}

/**
    * Verifica se a receita secundária informada é secundaria de alguma outra
    * @access Public
    * @param  Integer $inCodigoReceitaSecundaria Codigo da conta secundaria a ser validada
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaReceitaSecundaria2(&$rsLista, $inCodigoReceitaSecundaria, $boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

     $stFiltro  = " WHERE ";
     $stFiltro .= " exercicio = '".$this->roROrcamentoReceitaPrincipal->getExercicio()."' AND ";
     $stFiltro .= " cod_receita_principal != ".$this->roROrcamentoReceitaPrincipal->getCodReceita()." AND ";
     $stFiltro .= " cod_receita_secundaria = ".$inCodigoReceitaSecundaria;
     $obErro = $obTContabilidadeDesdobramentoReceita->recuperaTodos( $rsLista, $stFiltro, "", $boTransacao );

     return $obErro;
}

/**
    * Verifica se a receita secundária informada é principal de alguma outra
    * @access Public
    * @param  Integer $inCodigoReceitaSecundaria Codigo da conta secundaria a ser validada
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaReceitaSecundaria3(&$rsLista, $inCodigoReceitaSecundaria, $boTransacao = "")
{
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita = new TContabilidadeDesdobramentoReceita;

     $stFiltro  = " WHERE ";
     $stFiltro .= " exercicio = '".$this->roROrcamentoReceitaPrincipal->getExercicio()."' AND ";
     $stFiltro .= " cod_receita_principal = ".$inCodigoReceitaSecundaria;
     $obErro = $obTContabilidadeDesdobramentoReceita->recuperaTodos( $rsLista, $stFiltro, "", $boTransacao );

     return $obErro;
}

}
?>

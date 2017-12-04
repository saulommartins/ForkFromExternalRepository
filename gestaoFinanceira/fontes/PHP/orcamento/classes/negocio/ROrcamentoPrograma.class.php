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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.12  2007/10/02 18:28:00  leandro.zis
Ticket#9844#

Revision 1.11  2007/02/08 18:01:56  rodrigo_sr
Bug #7994#

Revision 1.10  2007/02/06 17:14:52  luciano
#8288#

Revision 1.9  2007/02/06 15:16:57  luciano
#8286#

Revision 1.8  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"        );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

/**
    * Classe de Regra de Negócio Itens
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino
*/
class ROrcamentoPrograma
{
/**
    * @var Objeto
    * @access Private
*/
var $obRConfiguracaoOrcamento;
/**
    * @var String
    * @access Private
*/
var $stMascara;
/**
    * @var Object
    * @access Private
*/
var $obTOrcamentoPrograma;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoPrograma;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stDescricao;

/**
    * @access Public
    * @param Object $valor
*/
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara                 = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTOrcamentoPrograma($valor) { $this->obTOrcamentoPrograma = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodPrograma($valor) { $this->inCodigoPrograma     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio          = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao          = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;      }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;      }
/**
     * @access Public
     * @return Object
*/
function getTOrcamentoPrograma() { return $this->obTOrcamentoPrograma; }
/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;          }
/**
     * @access Public
     * @return Integer
*/
function getCodPrograma() { return $this->inCodigoPrograma;     }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;          }
/**
     * @access Public
     * @return String
*/
function getDescricao() { return $this->stDescricao;          }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoPrograma()
{
    $this->setRConfiguracaoOrcamento( new ROrcamentoConfiguracao );
    $this->setTransacao             ( new Transacao              );
    $this->setExercicio             ( Sessao::getExercicio()         );
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrograma.class.php"  );
    $obTOrcamentoPrograma           = new TOrcamentoPrograma;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE cod_programa = ".$this->getCodPrograma();
        $obErro = $obTOrcamentoPrograma->recuperaTodos($rsPrograma, $stFiltro,'',$boTransacao);
        if ( !$obErro->ocorreu() ) {
            if ( $rsPrograma->eof() ) {
                $obTOrcamentoPrograma->setDado( "cod_programa"  , $this->getCodPrograma() );
                   $obTOrcamentoPrograma->setDado( "exercicio"     , $this->getExercicio()   );
                $obTOrcamentoPrograma->setDado( "descricao"     , $this->getDescricao()   );
                $obErro = $obTOrcamentoPrograma->inclusao( $boTransacao );
            } else {
                $obErro->setDescricao("Código ".$this->getCodPrograma()." já cadastrado!");
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoPrograma );
    }

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrograma.class.php"  );
    $obTOrcamentoPrograma           = new TOrcamentoPrograma;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoPrograma->setDado( "cod_programa"  , $this->getCodPrograma() );
        $obTOrcamentoPrograma->setDado( "exercicio"     , $this->getExercicio()   );
        $obTOrcamentoPrograma->setDado( "descricao"     , $this->getDescricao()   );
        $obErro = $obTOrcamentoPrograma->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoPrograma );
        }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrograma.class.php"  );
    $obTOrcamentoPrograma           = new TOrcamentoPrograma;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoPrograma->setDado( "cod_programa" , $this->getCodPrograma() );
        $obTOrcamentoPrograma->setDado( "exercicio"    , $this->getExercicio()           );
        $obErro = $obTOrcamentoPrograma->exclusao( $boTransacao );
        if ($obErro->ocorreu()) {
            $obErro->setDescricao('Programa não pode ser excluído porque está sendo utilizado.');
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoPrograma );
    }

    return $obErro;
}

/**
    * Executa um recuperaMascarado na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrograma.class.php"  );
    $obTOrcamentoPrograma           = new TOrcamentoPrograma;

    $this->pegarMascara($obTOrcamentoPrograma);
    $stFiltro = "";
    if ( $this->getCodPrograma() ) {
        $stFiltro .= " orcamento.programa.cod_programa = ".$this->getCodPrograma()." AND";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " (orcamento.programa.exercicio    = '" . $this->getExercicio() . "' OR orcamento.programa.exercicio = '" . Sessao::getExercicio() . "') AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " lower(orcamento.programa.descricao)  like lower('%".$this->getDescricao()."%') AND";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $obTOrcamentoPrograma->recuperaMascarado( $rsLista, $stFiltro, $stOrder, $obTransacao );
    
    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarSemMascara(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrograma.class.php"  );
    $obTOrcamentoPrograma           = new TOrcamentoPrograma;

    $this->pegarMascara($obTOrcamentoPrograma);
    $stFiltro = "";
    if ( $this->getCodPrograma() ) {
        $stFiltro .= " cod_programa = ".$this->getCodPrograma()." AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " exercicio = '".$this->getExercicio()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " lower(descricao)  like lower('%".$this->getDescricao()."%') AND";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $obTOrcamentoPrograma->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrograma.class.php"  );
    $obTOrcamentoPrograma           = new TOrcamentoPrograma;

    $obTOrcamentoPrograma->setDado( "exercicio"    , $this->getExercicio()    );
    $obTOrcamentoPrograma->setDado( "cod_programa" , $this->getCodPrograma()  );
    $obErro = $obTOrcamentoPrograma->recuperaPorChave( $rsLista, $obTransacao );

    return $obErro;
}

/**
    * Recupera a mascara do PAO
    * @access Public
*/
function pegarMascara(&$obTOrcamentoProgramaParametro)
{
    $obErro = new Erro;

    $stMascara = $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa');
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo X;
    $stMascara = $arMarcara[4];
    $this->setMascara( $stMascara );
    $obTOrcamentoProgramaParametro->setDado( "stMascara"  , $this->getMascara() );

    return $obErro;
}

}

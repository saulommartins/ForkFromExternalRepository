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
  * Classe de negócio Organograma
  * Data de Criação: 25/07/2005

  * @author Analista: Cassiano
  * @author Desenvolvedor: Cassiano

  Casos de uso: uc-01.05.02

  $Id: ROrganogramaOrganograma.class.php 61288 2014-12-30 12:29:30Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgaoNivel.class.php";
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaNivel.class.php";
include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";

class ROrganogramaOrganograma
{
/**
    * @access Private
    * @var Integer
*/
var $inCodOrganograma;
/**
    * @access Private
    * @var String
*/
var $stDtImplantacao;
/**
    * @access Private
    * @var Array
*/
var $arNivel;
/**
    * @access Private
    * @var Object
*/
var $obUltimoNivel;
/**
    * @access Private
    * @var Object
*/
var $obRNivel;
/**
    * @access Private
    * @var Object
*/
var $obRNorma;
/**
    * @access Private
    * @var Object
*/
var $obTOrganograma;
/**
    * @access Private
    * @var Object
*/
var $obTOrgaoNivel;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Boolean
*/
var $boAtivo;
/**
    * @access Private
    * @var Boolean
*/
var $boPermissaoHierarquica;
/**
    * @access Private
    * @var Boolean
*/
var $boMostraUltimoNivel;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodOrganograma($valor) { $this->inCodOrganograma = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDtImplantacao($valor) { $this->stDtImplantacao = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setUltimoNivel($valor) { $this->obUltimoNivel = $valor; }
/**
     * @access Public
     * @param Array $valor
*/
function setNivel($valor) { $this->arNivel = $valor;  }
/**
    * @access Public
    * @param Object $Valor
*/
function setTOrganograma($valor) { $this->obTOrganograma = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTOrgaoNivel($valor) { $this->obTOrgaoNivel = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRNivel($valor) { $this->obRNivel = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRNorma($valor) { $this->obRNorma = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setAtivo($valor) { $this->boAtivo = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setPermissaoHierarquica($valor) { $this->boPermissaoHierarquica = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setMostraUltimoNivel($valor) { $this->boMostraUltimoNivel = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrganograma() { return $this->inCodOrganograma; }
/**
    * @access Public
    * @return String
*/
function getDtImplantacao() { return $this->stDtImplantacao; }
/**
     * @access Public
     * @return Object
*/
function getUltimoNivel() { return $this->obUltimoNivel; }
/**
     * @access Public
     * @return Array
*/
function getNivel() { return $this->arNivel;  }
/**
    * @access Public
    * @return Object
*/
function getTOrganograma() { return $this->obTOrganograma; }
/**
    * @access Public
    * @return Object
*/
function getTOrgaoNivel() { return $this->obTOrgaoNivel; }
/**
    * @access Public
    * @return Object
*/
function getRNivel() { return $this->obRNivel; }
/**
    * @access Public
    * @return Object
*/
function getRNorma() { return $this->obRNorma; }
/**
    * @access Public
    * @return Boolean
*/
function getAtivo() { return $this->boAtivo; }
/**
    * @access Public
    * @return Boolean
*/
function getPermissaoHierarquica() { return $this->boPermissaoHierarquica; }
/**
    * @access Public
    * @return Boolean
*/
function getMostraUltimoNivel() { return $this->boMostraUltimoNivel ; }

/**
     * Método construtor
     * @access Private
*/
function ROrganogramaOrganograma()
{
    $this->setTOrganograma     ( new TOrganogramaOrganograma );
    $this->setTOrgaoNivel      ( new TOrganogramaOrgaoNivel  );
    $this->setRNorma           ( new RNorma                  );
    $this->setRNivel           ( new ROrganogramaNivel       );
    $this->obTransacao          = new Transacao;
    $this->arNivel              = array();
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

    if (!$obErro->ocorreu()) {

        $this->obTOrganograma->setDado("implantacao"           , $this->getDtImplantacao());
        $this->obTOrganograma->setDado("cod_norma"             , $this->obRNorma->getCodNorma());
        $this->obTOrganograma->setDado("permissao_hierarquica" , $this->getPermissaoHierarquica());

        # Ação de Alterar.
        if ($this->getCodOrganograma()) {
            $this->obTOrganograma->setDado("cod_organograma", $this->getCodOrganograma());
            $obErro = $this->obTOrganograma->alteracao( $boTransacao );
        } else {
            # Novo Organograma
            $DtImplantacao = explode ("/", $this->getDtImplantacao ());
            $DtImplantacao = $DtImplantacao[2]."-".$DtImplantacao[1]."-".$DtImplantacao[0];
            $DtAtual       = getdate ();
            $DtMesAtual    = str_pad($DtAtual["mon"], 2, "0", STR_PAD_LEFT);
            $DtAtualBanco  = $DtAtual["mday"]."/".$DtMesAtual."/".$DtAtual["year"];
            $DtAtual       = $DtAtual["year"]."-".$DtMesAtual."-".$DtAtual["mday"];

            # Não permite inserir um Organograma com data de vigência menor ao
            # Organograma ativo.
            if ($DtImplantacao < $DtAtual) {
                $stOrder  = " ORDER BY implantacao ";
                $stFiltro = " WHERE to_char(implantacao,'dd/mm/yyyy') < '".$DtAtualBanco."' ";
                $obErro = $this->obTOrganograma->recuperaTodos( $rsOrganograma, $stFiltro, $stOrder, $boTransacao );
                $rsOrganograma->setUltimoElemento();
                $DtMaiorOrganograma = explode ("/", $rsOrganograma->getCampo("implantacao"));
                $DtMaiorOrganograma = $DtMaiorOrganograma[2]."-".$DtMaiorOrganograma[1]."-".$DtMaiorOrganograma[0];

                if ($DtMaiorOrganograma > $DtImplantacao) {
                    $obErro->setDescricao ("Impossível inserir organograma com data anterior ao organograma vigente!");
                }
            }

            $this->obTOrganograma->recuperaTodos($rsOrganograma, "", "", $boTransacao);
            $inNumLinhas = $rsOrganograma->getNumLinhas();

            $boAtivo = false;
            # Validação para desconsiderar o Organograma = 0, cadastrado na
            # implantação do sistema.
            if ($inNumLinhas == 1) {

                $inCodOrganogramaAtual = $rsOrganograma->getCampo('cod_organograma');
                if ($inCodOrganogramaAtual == 0 && $rsOrganograma->getCampo('ativo') == 't' ) {
                    $boAtivo = true;
                }
            }

            while (!$rsOrganograma->eof() && !$obErro->ocorreu()) {
                if ($rsOrganograma->getCampo('implantacao') == $this->getDtImplantacao() ) {
                    $obErro->setDescricao('Esta data de Implantação já existe.');
                    break;
                }

                $rsOrganograma->proximo();
            }

            if (!$obErro->ocorreu()) {

                # Desativa o Organograma antigo (cadastrado na instalação do sistema)
                if ($boAtivo == true) {
                    $rsOrganograma->setPrimeiroElemento();
                    $this->obTOrganograma->setDado("cod_organograma" , $rsOrganograma->getCampo('cod_organograma'));
                    $this->obTOrganograma->setDado("cod_norma"       , $rsOrganograma->getCampo('cod_norma'));
                    $this->obTOrganograma->setDado("implantacao"     , $rsOrganograma->getCampo('implantacao'));
                    $this->obTOrganograma->setDado("ativo"           , false);
                    $this->obTOrganograma->alteracao($boTransacao);
                }

                $this->obTOrganograma->proximoCod( $inCodOrganograma , $boTransacao );
                $this->setCodOrganograma( $inCodOrganograma );
                $this->obTOrganograma->setDado("cod_organograma"       , $this->getCodOrganograma());
                $this->obTOrganograma->setDado("implantacao"           , $this->getDtImplantacao());
                $this->obTOrganograma->setDado("cod_norma"             , $this->obRNorma->getCodNorma());
                $this->obTOrganograma->setDado("ativo"                 , $boAtivo);
                $obErro = $this->obTOrganograma->inclusao($boTransacao);
            }
        }

        if (!$obErro->ocorreu()) {

            $obErro = $this->salvarNiveis( $boTransacao );
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
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
    
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->excluirNiveis( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTOrganograma->setDado("cod_organograma", $this->getCodOrganograma() );
            $obErro = $this->obTOrganograma->exclusao( $boTransacao );
        }        

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrganograma );
    }

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Filtro
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $stFiltroInicial = " WHERE  1=1 ";

    $stFiltro = ($stFiltro) ? $stFiltroInicial.$stFiltro : $stFiltroInicial;

    if ($this->inCodOrganograma) {
        $stFiltro .= " AND  cod_organograma = ".$this->inCodOrganograma;
    }

    if ($this->stDtImplantacao) {
        $stFiltro .= " AND  implantacao = '".$this->stDtImplantacao."'";
    }

    if( $this->obRNorma->getCodNorma() )
        $stFiltro .= " AND  cod_norma = ".$this->obRNorma->getCodNorma();

/*    if( $this->obUltimoNivel )
        if( $this->obUltimoNivel->getCodNivel() )
            $stFiltro .= " cod_nivel = " . $this->obUltimoNivel->getCodNivel() . " AND ";*/
//    $stFiltro .= " to_date(to_char(implantacao,'dd/mm/yyyy'),'dd/mm/yyyy') >= to_date(to_char(now(),'dd/mm/yyyy'),'dd/mm/yyyy')  AND ";

    $stOrder  = ($stOrder)  ? $stOrder : " to_char(implantacao,'dd/mm/yyyy') ";

    $obErro = $this->obTOrganograma->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarNiveis(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if ($this->inCodOrganograma) {
        $stFiltro = " cod_organograma = " . $this->inCodOrganograma . " AND ";
    }

    if ($this->boMostraUltimoNivel) {
        $stFiltro .= " cod_nivel = ( SELECT max(cod_nivel)    \n ";
        $stFiltro .= "                 FROM organograma.nivel \n ";
        $stFiltro .= "                WHERE cod_organograma = ".$this->inCodOrganograma.") AND ";
    }

    //$stOrder  = ($stOrder)  ? $stOrder : " ORDER BY num_nivel ";
    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";
    $obErro = $this->obRNivel->obTNivel->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function consultarNivel($boTransacao = "")
{
    $this->obRNivel->obTNivel->setDado("cod_organograma" , $this->inCodOrganograma );
    $this->obRNivel->obTNivel->setDado("cod_nivel"       , $this->obRNivel->getCodNivel() );
    $obErro = $this->obRNivel->obTNivel->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obRNivel->setDescricao       ( $rsRecordSet->getCampo('descricao') );
        $this->obRNivel->setMascaraCodigo   ( $rsRecordSet->getCampo('mascaracodigo') );
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
function listarOrgaosRelacionados(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro  = "";
    if( $this->inCodOrganograma )
        $stFiltro .= " cod_organograma = " . $this->inCodOrganograma . " AND ";
    if( $this->obRNivel->getCodNivel() )
        $stFiltro .= " cod_nivel = " . $this->obRNivel->getCodNivel() . " AND ";

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";
    $obErro = $this->obTOrgaoNivel->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaOrgaoDescricao
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrgaosRelacionadosDescricao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if ($this->inCodOrganograma) {
        $stFiltro = " tabela.cod_organograma = " . $this->inCodOrganograma . " AND ";
    }

    if ($this->obRNivel->getCodNivel()) {
        $stFiltro .= " tabela.cod_nivel = " . $this->obRNivel->getCodNivel() . " AND ";
        $stFiltro .= " tabela.nivel = " . $this->obRNivel->getCodNivel() . " AND ";
    }

    if ($this->obRNivel->getMascaraCodigo()) {
        $stFiltro .= " tabela.orgao like '".$this->obRNivel->getMascaraCodigo()."%' AND ";
    }

    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";
    $obErro = $this->obTOrgaoNivel->recuperaOrgaoDescricao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaOrgaoDescricaoComponente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrgaosRelacionadosDescricaoComponente(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = " WHERE tabela.cod_nivel = tabela.nivel ";
    if ($this->inCodOrganograma) {
        $stFiltro .= " AND tabela.cod_organograma = " . $this->inCodOrganograma;
    }

    if ($this->obRNivel->getCodNivel()) {
        $stFiltro .= " AND tabela.cod_nivel = " . $this->obRNivel->getCodNivel();
    }

    if ($this->obRNivel->getMascaraCodigo()) {
        $stFiltro .= " AND tabela.orgao like '".$this->obRNivel->getMascaraCodigo()."%' ";
    }

    $stOrder = " ORDER BY tabela.cod_nivel, tabela.orgao ";
    $obErro = $this->obTOrgaoNivel->recuperaOrgaoDescricaoComponente( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
    $this->obTOrganograma->setDado( "cod_organograma" , $this->inCodOrganograma );
    $obErro = $this->obTOrganograma->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stDtImplantacao = $rsRecordSet->getCampo("implantacao");
        $this->obRNorma->setCodNorma( $rsRecordSet->getCampo("cod_norma") );
        $obErro = $this->obRNorma->consultar( $rsNorma );
    }

    return $obErro;
}
/**
    * Salva dados de Niveis no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarNiveis($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );    
    if ( !$obErro->ocorreu() ) {
        //Se nao for Acao de Alteracao
        if (!$this->getCodOrganograma())
            $obErro = $this->excluirNiveis( $boTransacao );        
        if ( !$obErro->ocorreu() ) {
            $arNiveis = $this->getNivel();
            foreach ($arNiveis as $obNivel) {
                $obNivel->obTNivel->setDado("cod_organograma" , $this->getCodOrganograma()   );
//                $obNivel->obTNivel->setDado("num_nivel"       , $obNivel->getNumNivel()      );
                $obNivel->obTNivel->setDado("descricao"       , $obNivel->getDescricao()     );
                $obNivel->obTNivel->setDado("mascaracodigo"   , $obNivel->getMascaraCodigo() );
                if ( $obNivel->getCodNivel() ) {
                    $obNivel->obTNivel->setDado("cod_nivel" , $obNivel->getCodNivel() );
                    $obErro = $obNivel->obTNivel->alteracao( $boTransacao );
                } else {
                    $obNivel->obTNivel->proximoCod( $inCodNivel , $boTransacao );
                    $obNivel->obTNivel->setDado("cod_nivel"     , $inCodNivel );
                    $obErro = $obNivel->obTNivel->inclusao( $boTransacao );
                }
                if( $obErro->ocorreu() )
                    break;
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}
/**
    * Exclui dados de Nivel do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirNiveis($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE cod_organograma=".$this->getCodOrganograma();
        $this->obRNivel->obTNivel->recuperaTodos( $rsNiveis , $stFiltro , '' , $boTransacao );
        
        while ( !$rsNiveis->eof() ) {
            $this->obRNivel->obTNivel->setDado("cod_nivel"      , $rsNiveis->getCampo("cod_nivel") );
            $this->obRNivel->obTNivel->setDado("cod_organograma", $this->getCodOrganograma() );
            $obErro = $this->obRNivel->obTNivel->exclusao( $boTransacao );
            if( $obErro->ocorreu() )
                break;
            $rsNiveis->proximo();
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}

/**
    * Recupera organograma que nao possui orgao relacionado
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrganogramas(&$rsRecordSet, $stOrder = "", $boTransacao = "", $stFiltro="")
{
    $stOrder = (empty($stOrder) ? " ORDER BY to_char (implantacao, 'dd/mm/yyyy') " : $stOrder);
    $obErro = $this->obTOrganograma->recuperaOrganogramas( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function recuperaClassificacaoOrgao(&$rsRecordSet, $stFiltro="", $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTOrgaoNivel->recuperaOrgaoDescricao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}

?>

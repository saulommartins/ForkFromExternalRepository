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
    * Classe de Regra de Negócio Relatório de Histórico Padrao
    * Data de Criação   : 25/11/2004

    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.20
*/

/*
$Log$
Revision 1.7  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_CONT_MAPEAMENTO. "TContabilidadeHistoricoContabil.class.php" );
include_once( CAM_GF_CONT_NEGOCIO.    "RContabilidadeHistoricoPadrao.class.php"   );
/**
    * Classe de Regra de Negócio Relatório de Histórico Padrao
    * @author Desenvolvedor: Eduardo Martins
*/
class RRelatorioHistoricoPadrao  extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRHistoricoPadrao;

/**
    * @var Integer
    * @access Private
*/
var $inCodHistoricoPadraoIni;

/**
    * @var Integer
    * @access Private
*/
var $inCodHistoricoPadraoFim;

/**
    * @var String
    * @access Private
*/
var $stDescricao;

/**
    * @var String
    * @access Private
*/
var $stComComplemento;

/**
    * @var String
    * @access Private
*/
var $stOrdenacao;

/**
     * @access Public
     * @param Object $valor
*/
function setRHistoricoPadrao($valor) { $this->obRHistoricoPadrao = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodHistoricoPadraoIni($valor) { $this->inCodHistoricoPadraoIni = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodHistoricoPadraoFim($valor) { $this->inCodHistoricoPadraoFim = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setComComplemento($valor) { $this->stComComplemento = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setOrdenacao($valor) { $this->stOrdenacao = $valor; }

/**
     * @access Public
     * @return Object
*/
function getRHistoricoPadrao() { return $this->obRHistoricoPadrao;           }

/**
     * @access Public
     * @return Object
*/
function getCodHistoricoPadraoIni() { return $this->inCodHistoricoPadraoIni;           }

/**
     * @access Public
     * @return Object
*/
function getCodHistoricoPadraoFim() { return $this->inCodHistoricoPadraoFim;           }

/**
     * @access Public
     * @return Object
*/
function getDescricao() { return $this->stDescricao;           }

/**
     * @access Public
     * @return Object
*/
function getComComplemento() { return $this->stComComplemento;           }

/**
     * @access Public
     * @return Object
*/
function getOrdenacao() { return $this->stOrdenacao;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioHistoricoPadrao()
{
    $this->setRHistoricoPadrao   ( new RContabilidadeHistoricoPadrao   );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsHistoricoPadrao, $stOrder = "cod_historico")
{
    $this->obRHistoricoPadrao->setNomHistorico($this->getDescricao());
    
    switch ($this->getComComplemento()) {
        case 'a':
            $this->obRHistoricoPadrao->setComplemento('ambos');
        break;

        case 's':
            $this->obRHistoricoPadrao->setComplemento('true');
        break;
    
        case 'n':
            $this->obRHistoricoPadrao->setComplemento('false');
        break;
    }
    
    if ($this->getCodHistoricoPadraoIni()){
        $stFiltroHistorico = " AND cod_historico >= ".$this->getCodHistoricoPadraoIni()."";
    }
    
    if ($this->getCodHistoricoPadraoFim()){
        $stFiltroHistorico .= " AND cod_historico <= ".$this->getCodHistoricoPadraoFim()."";
    }
    
    if ($stFiltroHistorico) {
        $this->obRHistoricoPadrao->setFiltroCodHistorico($stFiltroHistorico);
    }
    
    $stOrder = $this->getOrdenacao();
    
    $this->obRHistoricoPadrao->listarRelatorio( $rsRecordSet, $stOrder );
    
    $inCount = 0;
    
    while ( !$rsRecordSet->eof() ) {
        
        $arHistorico[$inCount]['cod_historico'] = $rsRecordSet->getCampo('cod_historico');
        $arHistorico[$inCount]['nom_historico'] = $rsRecordSet->getCampo('nom_historico');
        
        $inCount++;
        
        $rsRecordSet->proximo();
    }
    
    $rsHistoricoPadrao = new RecordSet;
    $rsHistoricoPadrao->preenche( $arHistorico );

    return $obErro;
}

}

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
* Classe de negócio Norma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO  );
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"            );

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: Marcia Luisa Merker
*/
class RNormasNorma extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRNorma;

/**
     * @access Public
     * @param Object $valor
*/
function setRNorma($valor) { $this->obRNorma = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRNorma() { return $this->obRNorma;           }

/**
    * Método Construtor
    * @access Private
*/
function RNormasNorma()
{
    $this->setRNorma( new RNorma );
}

/**
    * Método abstrato
    * @access Public
*/
    function geraRecordSet(&$rsNorma , $stOrder = "num_norma,exercicio")
    {
        $arFiltroFormulario = Sessao::read('filtroRelatorio');
    
        switch ($arFiltroFormulario['stCtrl']) {
            case "vigente":
                $this->obRNorma->listarGenerico( $rsRecordSet, $stOrder, "vigente" );
                break;
    
            case "revogada":
                $this->obRNorma->listarGenerico( $rsRecordSet, $stOrder, "revogada" );
                break;
    
            case "vigente_ate":
                $this->obRNorma->listarGenerico( $rsRecordSet, $stOrder, "vigente_ate" );
                break;
        }
       
        $arNorma = array();
        $inCount       = 0;
    
        while ( !$rsRecordSet->eof() ) {
            $inSequencia = ($inCount + 1);
            $arNorma[$inCount]['sequencia']           = $inSequencia;
            $arNorma[$inCount]['num_norma_exercicio'] = $rsRecordSet->getCampo('num_norma_exercicio');
            $arNorma[$inCount]['descricao']           = $rsRecordSet->getCampo('descricao');
            $arNorma[$inCount]['dt_publicacao']       = $rsRecordSet->getCampo('dt_publicacao');
            $inCount++;
            $rsRecordSet->proximo();
        }
    
        $rsNorma = new RecordSet;
    
        $rsNorma->preenche( $arNorma );
    
        return $obErro;
    }

}

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
   /*
    * Classe de Mapeamento da tabela 
    * Data de Criação   : 04/02/2015

    * @author Analista      
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMGDespesaIntra extends Persistente
{

function recuperaDadosArquivo(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosArquivo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosArquivo()
{
    $stSql  = " SELECT mes
                     , REPLACE(demais_despesas_intra::VARCHAR,'.','') AS demais_despesas_intra
                     , tipo_conta
                     , '000'::VARCHAR AS juros_encargos_divida
                     , '000'::VARCHAR AS amort_divida
		  FROM tcemg.despesa_intra( '".Sessao::getExercicio()."','".$this->getDado('cod_entidade')."'
					   ,'".$this->getDado('dataInicial')."'
					   ,'".$this->getDado('dataFinal')."'
					   ,'".$this->getDado('mes')."') as retorno( mes integer,
                                                                 demais_despesas_intra numeric,          
                                                                 tipo_conta  text ) ";
																					
	
    return $stSql;
}

public function __destruct(){}


}

?>

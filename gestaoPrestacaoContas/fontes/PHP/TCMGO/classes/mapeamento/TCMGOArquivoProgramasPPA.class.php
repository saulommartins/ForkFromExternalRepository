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
 * Classe de mapeamento do arquivo PRO - ARQUIVO DOS PROGRAMAS DO PPA
 * Data de Criação: 28/01/2015
 * 
 * @author Analista      : Ane Pereira
 * @author Desenvolvedor : Arthur Cruz
 * 
 * @package URBEM
 * @subpackage Mapeamento
 *
 * $Id: $
 * $Revision: $
 * $Author: $
 * $Date: $
 * 
 */

class TCMGOArquivoProgramasPPA extends Persistente
{
    public function recuperaTotalRecursos(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaTotalRecursos($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaTotalRecursos($stFiltro = '', $stOrdem = '')
    {
        $stSql = "  SELECT 10 AS tipo_registro
                         , *                            
                    FROM tcmgo.recupera_ppa_programa('".$this->getDado('exercicio')."')
                      AS retorno (
                                    cod_programa          INTEGER
                                  , tipo_programa        INTEGER
                                  , nome_programa        VARCHAR
                                  , num_programa         INTEGER
                                  , objetivo             VARCHAR
                                  , total_recursos_ano_1 VARCHAR
                                  , total_recursos_ano_2 VARCHAR
                                  , total_recursos_ano_3 VARCHAR
                                  , total_recursos_ano_4 VARCHAR
                                )                    
                    ORDER BY num_programa ";
        return $stSql;
    }
    
    public function __destruct(){}
}

?>
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
    * Classe de mapeamento do arquivo orgao 
    * Data de Criação: 28/04/2016

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Revision:$
    $Name$
    $Author:$
    $Date:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOOrgao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */

    public function recuperaOrgao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaOrgao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaOrgao()
    {
        $stSql = "  SELECT '10' AS tipo_registro
                         , tcmgo.orgao.num_orgao
                         , cod_tipo AS tipo_orgao
                         , nom_orgao AS desc_orgao
                         , cgm_orgao_juridica.cnpj
                         , BTRIM(cgm_orgao.logradouro) || ' ' || BTRIM(cgm_orgao.numero) || ' ' || BTRIM(cgm_orgao.complemento)  AS logradouro
                         , BTRIM(cgm_orgao.bairro) AS setor
                         , cgm_orgao.cep
                         , 0 AS numero_sequencial
                      FROM orcamento.orgao
                INNER JOIN tcmgo.orgao
                        ON tcmgo.orgao.num_orgao = orcamento.orgao.num_orgao
                       AND tcmgo.orgao.exercicio = orcamento.orgao.exercicio
                INNER JOIN sw_cgm AS cgm_orgao
                        ON cgm_orgao.numcgm = numcgm_orgao
                INNER JOIN sw_cgm_pessoa_juridica AS cgm_orgao_juridica
                        ON cgm_orgao_juridica.numcgm = cgm_orgao.numcgm
                     WHERE tcmgo.orgao.exercicio = '".$this->getDado('exercicio')."'";
        return $stSql;
    }
    
    public function __destruct(){}
    
}
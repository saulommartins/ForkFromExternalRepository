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
    * Extensão da Classe de mapeamento
    * Data de Criação: 09/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCETOContaReceitaOrcamentaria extends Persistente
{

    function montaRecuperaTodos()
    {
        $stSql = " SELECT 
                            ( SELECT PJ.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                    ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS PJ
                                    ON sw_cgm.numcgm = PJ.numcgm
                                WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = ".$this->getDado('entidade')." 
                            )AS cod_und_gestora                            
                            ,conta_receita.exercicio
                            ,REPLACE(conta_receita.cod_estrutural,'.','') as  conta_receita
                            ,TRIM(conta_receita.descricao) AS nome
                            ,orcamento.fn_tipo_conta_receita('".$this->getDado('exercicio')."', conta_receita.cod_estrutural) as tipo_nivel
                            ,publico.fn_nivel(conta_receita.cod_estrutural) as nivel    

                    FROM orcamento.conta_receita
                     
                    WHERE conta_receita.exercicio = '".$this->getDado('exercicio')."'
                    ORDER BY cod_estrutural
        ";
        
        return $stSql;
    }

}//FIM CLASSE

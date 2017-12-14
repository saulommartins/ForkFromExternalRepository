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
    * 
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEPrevisaoReceita.class.php 60188 2014-10-03 21:03:19Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEPrevisaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEPrevisaoReceita()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT   
                             receita.exercicio   
                            , REPLACE(conta_receita.cod_estrutural,'.','') as codigo_receita      
                            , ABS(vl_original) as vl_original
                            , ".$this->getDado('unidade_gestora')." as unidade_gestora
                    
                    FROM orcamento.receita       
                    JOIN orcamento.conta_receita 
                         ON receita.exercicio = conta_receita.exercicio   
                        AND receita.cod_conta = conta_receita.cod_conta   
                        
                    WHERE receita.exercicio = '".$this->getDado('exercicio')."' 
                    AND receita.cod_entidade IN (".$this->getDado('cod_entidade').") 
                    AND ABS(vl_original) > 0 

                    ORDER BY cod_estrutural 
                ";

        return $stSql;
    }
}

?>
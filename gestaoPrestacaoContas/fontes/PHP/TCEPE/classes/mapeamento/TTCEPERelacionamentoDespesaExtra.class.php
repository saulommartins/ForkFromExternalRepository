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
    $Id: TTCEPERelacionamentoDespesaExtra.class.php 60179 2014-10-03 18:08:53Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPERelacionamentoDespesaExtra extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPERelacionamentoDespesaExtra()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = " SELECT  
                            REPLACE(plano_conta.cod_estrutural,'.','') as cod_conta
                            ,  plano_analitica_relacionamento.cod_relacionamento AS cod_relacionamento_despesa
                        FROM  tcepe.plano_analitica_relacionamento
                        JOIN  contabilidade.plano_analitica
                             ON  plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                            AND  plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                        JOIN  contabilidade.plano_conta
                             ON  plano_conta.cod_conta = plano_analitica.cod_conta
                            AND  plano_conta.exercicio = plano_analitica.exercicio
        ";
        return $stSql;
    }
}

?>
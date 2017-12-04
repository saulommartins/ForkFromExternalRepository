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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 30/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id: FTCEMGReceitaCorrente.class.php 63423 2015-08-26 20:27:31Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGReceitaCorrente extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function FTCEMGReceitaCorrente()
    {
        parent::Persistente();
    
       $this->setTabela('tcemg.fn_receita_corrente');
    
       $this->AddCampo('stExercicio'            , 'varchar', false, '', false, false);
       $this->AddCampo('stCodEntidades'         , 'varchar', false, '', false, false);
       $this->AddCampo('inBimestre'             , 'integer', false, '', false, false);
    
    }
    
    function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT EXTRACT( month FROM TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') ) AS mes
                 , cod_estrutural
                 , valor_previsto
                 , arrecadado_periodo
                 , arrecadado_ano
                 , diferenca
             FROM ".$this->getTabela()." ( '".$this->getDado("stExercicio")."'
                                         , '".$this->getDado("stCodEntidades")."'
                                         , '".$this->getDado("dt_inicial")."'
                                         , '".$this->getDado("dt_final")."'
                                         )
               AS retorno                ( cod_estrutural      VARCHAR ,                                           
                                           valor_previsto      NUMERIC ,
                                           arrecadado_periodo  NUMERIC ,
                                           arrecadado_ano      NUMERIC ,
                                           diferenca           NUMERIC
                                         )
         ORDER BY cod_estrutural; ";

        return $stSql;
    }
}

?>
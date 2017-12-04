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
    * Data de Criação: 22/02/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
    
    $Id: TTPBDecreto.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.2  2007/04/23 15:22:11  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/02/22 23:24:48  diego
Primeira versão...

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );

/**
  *
  * Data de Criação: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBDecreto extends TNorma
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBDecreto()
{
    parent::TNorma();
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{  
    $stSql .= " SELECT norma.num_norma AS nro_decreto
                     , suplementacao.cod_norma                          
	             , norma.exercicio AS exercicio_decreto                            
	             , tcers.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Número da Lei') AS nro_lei                    
	             , substr( tcers.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Data da Lei'),7,4) AS exercicio_lei   
	             , TO_CHAR(norma.dt_assinatura,'ddmmyyyy') AS data_decreto           
	             , CASE WHEN tipo_norma.nom_tipo_norma ~* 'decreto' THEN 1                                                       
		            WHEN tipo_norma.nom_tipo_norma ~* 'oficio'  THEN 2                                                       
	               END AS tipo_documento                                           

                  FROM normas.norma
                      
            INNER JOIN normas.tipo_norma
                    ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma
                  
            INNER JOIN orcamento.suplementacao
                    ON suplementacao.cod_norma = norma.cod_norma
                  
              LEFT JOIN orcamento.suplementacao_suplementada
                     ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                    AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                  
              LEFT JOIN orcamento.despesa   
                     ON despesa.exercicio   = suplementacao_suplementada.exercicio
                    AND despesa.cod_despesa = suplementacao_suplementada.cod_despesa
                  
                  WHERE TO_CHAR(norma.dt_publicacao,'mm') = '".$this->getDado('inMes')."'
                    AND norma.exercicio = '".$this->getDado('exercicio')."'
                    AND despesa.cod_entidade IN ( ".$this->getDado('stEntidades')." )  
                    AND (tipo_norma.nom_tipo_norma ~* 'decreto' OR tipo_norma.nom_tipo_norma ~* 'oficio')   
                                              
                GROUP BY norma.num_norma
                      , suplementacao.cod_norma
                      , norma.exercicio
                      , nro_lei
                      , exercicio_lei
                      , norma.dt_assinatura
                      , tipo_norma.nom_tipo_norma                        
                  
                  ORDER BY norma.exercicio
                      , norma.num_norma ";

    return $stSql;
}

}

?>
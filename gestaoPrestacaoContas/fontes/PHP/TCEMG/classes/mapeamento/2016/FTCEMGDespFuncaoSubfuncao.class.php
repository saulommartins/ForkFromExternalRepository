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
    * Data de Criação   : 16/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id: FTCEMGDespFuncaoSubfuncao.class.php 63314 2015-08-17 13:48:57Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FTCEMGDespFuncaoSubfuncao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    
        $this->setTabela('tcemg.fn_desp_funcao_subfuncao');
    
        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('periodo'       ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
              SELECT periodo
                   , cod_vinculo
                   , vl_inicial
                   , vl_atualizada
                   , vl_empenhado
                   , vl_liquidado
                   , vl_anulada
                   , cod_funcao
                   , cod_subfuncao
                   , cod_entidade_relacionada
                FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                           , '".$this->getDado("cod_entidade")."'
                                           , '".$this->getDado("data_inicial")."'
                                           , '".$this->getDado("data_final")."'
                                           )
                  AS retorno ( periodo                  INTEGER
                             , cod_vinculo              VARCHAR
                             , vl_inicial               NUMERIC
                             , vl_atualizada            NUMERIC
                             , vl_empenhado             NUMERIC
                             , vl_liquidado             NUMERIC
                             , vl_anulada               NUMERIC
                             , cod_funcao               VARCHAR
                             , cod_subfuncao            VARCHAR
                             , cod_entidade_relacionada INTEGER
                             );
        ";
        return $stSql;
    }

    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct() {}

}

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
/*
    * Mapeamento tcmba.obra_medicao
    * Data de Criação   : 14/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBAObraMedicao.class.php 63809 2015-10-19 16:52:56Z lisiane $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAObraMedicao extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.obra_medicao');
        $this->setComplementoChave('cod_obra, cod_tipo, cod_entidade, exercicio, cod_medicao');

        $this->AddCampo('cod_obra'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('cod_entidade'          , 'integer' , true  , ''    , true , true );
        $this->AddCampo('exercicio'             , 'varchar' , true  , '4'   , true , true );
        $this->AddCampo('cod_tipo'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('cod_medicao'           , 'bigint'  , true  , ''    , true , false);
        $this->AddCampo('cod_medida'            , 'integer' , true  , ''    , false, true );
        $this->AddCampo('data_inicio'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_final'            , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_medicao'          , 'date'    , true  , ''    , false, false);
        $this->AddCampo('vl_medicao'            , 'numeric' , true  , '16,2', false, false);
        $this->AddCampo('nro_nota_fiscal'       , 'varchar' , true  , '20'  , false, false);
        $this->AddCampo('data_nota_fiscal'      , 'date'    , true  , ''    , false, false);
        $this->AddCampo('numcgm'                , 'integer' , true  , ''    , false, true );
    }
}


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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaMotivoInfracao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaMotivoInfracao()
    {
        parent::Persistente();
        $this->setTabela('frota.motivo_infracao');
        $this->setCampoCod('cod_infracao');

        $this->AddCampo('cod_infracao'    , 'integer', true, ''    , true , false);
        $this->AddCampo('descricao'       , 'varchar', true, '100' , false, false);
        $this->AddCampo('base_legal'      , 'varchar', true, '20'  , false, false);
        $this->AddCampo('gravidade'       , 'varchar', true, '20'  , false, false);
        $this->AddCampo('responsabilidade', 'varchar', true, '20'  , false, false);
        $this->AddCampo('competencia'     , 'varchar', true, '20'  , false, false);
        $this->AddCampo('pontos'          , 'integer', true, ''    , false, false);
        $this->AddCampo('valor'           , 'numeric', true, '14.2', false, false);
    }
}

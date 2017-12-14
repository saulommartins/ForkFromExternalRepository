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

class TTCMGOConfiguracaoOrgaoUnidade extends Persistente
{

/**
    * Método Construtor
    * @access Private
*/
    public function TTCMGOConfiguracaoOrgaoUnidade()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.configuracao_orgao_unidade');

        //$this->setCampoCod('cod_entidade');
        $this->setComplementoChave('exercicio,cod_entidade,cod_poder');

        $this->AddCampo('exercicio',    'char',    true, 4,  true,  true);
        $this->AddCampo('cod_entidade', 'integer', true, '', true,  true);
        $this->AddCampo('cod_poder',    'integer', true, '', true,  true);
        $this->AddCampo('num_orgao',    'integer', true, '', false, true);
        $this->AddCampo('num_unidade',  'integer', true, '', false, true);
    }
}

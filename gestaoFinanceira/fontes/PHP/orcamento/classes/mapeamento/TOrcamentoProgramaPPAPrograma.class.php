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
    * Classe de mapeamento da tabela orcamento.programa_ppa_programa
    * Data de Criação: 12/05/2009

    * @author Analista: Tonismar Régis Bernardo
    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TOrcamentoProgramaPPAPrograma extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrcamentoProgramaPPAPrograma()
    {
        parent::Persistente();

        $this->setTabela('orcamento.programa_ppa_programa');

        $this->setCampoCod('cod_programa');
        $this->setComplementoChave('exercicio, cod_programa_ppa');

        $this->AddCampo('cod_programa'    , 'integer', true, '', true, true);
        $this->AddCampo('cod_programa_ppa', 'integer', true, '', true, true);
        $this->AddCampo('exercicio'       , 'char', true, '4', true, true);
    }

 } // end of class

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
 * Classe de mapeamento da tabela PPA.receita
 * Data de Criação: 17/12/2008
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 * @ignore
 *
 * $Id: $
 *
 * Casos de uso: uc-02.09.05
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAReceitaInativaNorma extends Persistente
{

    /**
     * Método Construtor
     *
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ppa.ppa_receita_inativa_norma');
        $this->setCampoCod('cod_receita');
        $this->setComplementoChave('cod_ppa, exercicio, cod_conta, cod_entidade');
        // campo, tipo, not_null, data_length, pk, fk
        $this->AddCampo('cod_receita',  'integer', true, '',  true, true);
        $this->AddCampo('cod_ppa',      'integer', true, '',  true, true);
        $this->AddCampo('exercicio',    'char',    true, '4', true, true);
        $this->AddCampo('cod_conta',    'integer', true, '',  true, true);
        $this->AddCampo('cod_entidade', 'integer', true, '',  true, true);
        $this->AddCampo('cod_norma',    'integer', true, '',  false, true);
        #$this->AddCampo('timestamp',    'timestamp', true, '',  true, false);
    }

}
?>

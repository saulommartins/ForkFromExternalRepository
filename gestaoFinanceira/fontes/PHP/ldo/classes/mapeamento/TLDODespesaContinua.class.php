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
 * Classe de Mapeameto 02.10.14 - Manter Expansão das Despesas de Caráter Continuado
 * Data de Criação: 24/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.00 - Manter LDO
 */

class TLDODespesaContinua extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.despesa_continua');

        $this->setCampoCod('cod_despesa');

        $this->setComplementoChave('ano');

        $this->addCampo('cod_despesa', 'integer', true, '', true, true);
        $this->addCampo('ano', 'character', true, '4', true, true);
        $this->addCampo('aumento_permanente', 'numeric', false, '14, 2', false, false);
        $this->addCampo('transferencia_constitucional', 'numeric', true, '14, 2', false, false);
        $this->addCampo('transferencia_fundeb', 'numeric', true, '14, 2', false, false);
        $this->addCampo('reducao_permanente', 'numeric', true, '14, 2', false, false);
        $this->addCampo('saldo_utilizado_margem_bruta', 'numeric', true, '14, 2', false, false);
        $this->addCampo('docc', 'numeric', true, '14, 2', false, false);
        $this->addCampo('docc_ppp', 'numeric', true, '14, 2', false, false);
    }
}

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
 * Classe Mapeameto do 02.10.06 - Manter Riscos Fiscais
 * Data de Criação: 12/10/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.06 - Manter Riscos Fiscais
 */

class TLDOProvidenciaFiscal extends Persistente
{
    /**
     * Método construtor
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ldo.providencia_fiscal');
        $this->setCampoCod('cod_providencia_fiscal');
        $this->addCampo('cod_providencia_fiscal', 'integer', true,  '',    true,  false);
        $this->addCampo('descricao',              'varchar', true, '250',  false, false);
        $this->AddCampo('valor',                  'numeric', true, '14,2', false, false);
        $this->addCampo('cod_risco_fiscal',       'integer', true,  '',    false, true);
    }

}

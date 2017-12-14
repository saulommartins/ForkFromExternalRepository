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
 * Componente ISelectNotaExplicativa
 *
 * Data de Criação: 16/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage ldo
 * @uc 02.10.15
 */

include_once CLA_SELECT;
include_once CAM_GF_LDO_MAPEAMENTO . 'TLDONotaExplicativa.class.php';

class ISelectNotaExplicativa extends Select
{

    /**
     * Contrutor.
     *
     * @param int $inCodACao
     */
    public function __construct()
    {
        parent::__construct();

        $this->setRotulo('Nota Explicativa');
        $this->setName('inCodNotaExplicativa');
        $this->setId('inCodNotaExplicativa');
        $this->setTitle('Selecione a Nota Explicativa.');
        $this->setNull(true);
        $this->addOption('', 'Selecione');
        $this->setCampoID('cod_nota_explicativa');
        $this->setCampoDesc('descricao');
        $this->setStyle('width: 205px');
    }

    /**
     * Monta componente
     */
    public function montaHTML()
    {
        $obTMapeamento = new TLDONotaExplicativa();
        $stFiltro = ' WHERE a.cod_acao = ' . Sessao::read('acao');
        $obTMapeamento->recuperaNotaExplicativa($rsLista, $stFiltro);
        $this->preencheCombo($rsLista);
        parent::montaHTML();
    }

}

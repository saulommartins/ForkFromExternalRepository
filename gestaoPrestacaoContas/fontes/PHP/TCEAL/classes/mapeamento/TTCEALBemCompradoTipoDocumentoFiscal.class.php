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
 * Mapeamento da tabela tceal.bem_comprado_tipo_documento_fiscal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista       Silvia Martins 
 * @author      Desenvolvedor  Lisiane da Rosa Morais     
 * $Id:$
 */

require_once CLA_PERSISTENTE;

class TTCEALBemCompradoTipoDocumentoFiscal extends Persistente
{
    /**
     * Método Construtor da classe TCEALBemCompradoTipoDocumentoFiscal
     *
     * @author      Analista       Silvia Martins 
     * @author      Desenvolvedor  Lisiane da Rosa Morais     
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela          ('tceal.bem_comprado_tipo_documento_fiscal');
        $this->setCampoCod        ('cod_bem');
        $this->setComplementoChave('');

        $this->AddCampo('cod_bem'                    ,'integer',true,'', true , true );
        $this->AddCampo('cod_tipo_documento_fiscal'  ,'integer',true,'', true , true );
    }
}

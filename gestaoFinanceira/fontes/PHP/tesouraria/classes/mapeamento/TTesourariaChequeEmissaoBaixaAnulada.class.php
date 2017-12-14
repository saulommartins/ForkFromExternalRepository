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
 * Mapeamento da tabela tesouraria.cheque_emissao_baixa_anulada
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaChequeEmissaoBaixaAnulada extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaChequeEmissaoBaixaAnulada
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque_emissao_baixa_anulada');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_agencia, cod_banco, cod_conta_corrente, num_cheque, timestamp_emissao, timestamp_baixa');

        $this->AddCampo('cod_agencia'        ,'integer'  , true , ''     , true , true );
        $this->AddCampo('cod_banco'          ,'integer'  , true , ''     , true , true );
        $this->AddCampo('cod_conta_corrente' ,'integer'  , true , ''     , true , true );
        $this->AddCampo('num_cheque'         ,'varchar'  , true , '15'   , true , true );
        $this->AddCampo('timestamp_emissao'  ,'timestamp', true , ''     , true , true );
        $this->AddCampo('timestamp_baixa'    ,'timestamp', true , ''     , true , true );

    }

}

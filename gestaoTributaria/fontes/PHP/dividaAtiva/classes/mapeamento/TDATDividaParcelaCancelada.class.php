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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_PARCELA_CANCELADA
    * Data de Criação: 06/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaParcelaCancelada.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2006/10/06 17:00:12  dibueno
*** empty log message ***

Revision 1.2  2006/10/05 14:42:18  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/10/03 17:52:18  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaParcelaCancelada extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaParcelaCancelada()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_parcela_cancelada');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_inscricao, num_parcelamento, num_parcela');

        $this->AddCampo('exercicio_cancelada','varchar',true,'4',true,true);
        $this->AddCampo('cod_inscricao_cancelada','integer',true,'',true,true);
        $this->AddCampo('num_parcelamento_cancelada','integer',true,'',true,false);
        $this->AddCampo('num_parcela_cancelada','integer',true,'',true,true);

        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
        $this->AddCampo('num_parcela','integer',true,'',true,true);
        $this->AddCampo('exercicio','varchar',true,'4',true,true);

    }

}// end of class
?>

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
* Componente ISelectComissaoEquipeApoio

* Data de Criação: 17/10/2006

* @author Analista: Lucas Teixeiera Stephanou
* @author Desenvolvedor: Lucas Teixeiera Stephanou

Casos de uso: uc-03.05.09
              uc-03.05.15

*/

include_once ( CLA_SELECT );

class ISelectComissaoEquipeApoio extends ISelectComissao
{
    public function ISelectComissaoEquipeApoio()
    {
        include_once(CAM_GP_COM_MAPEAMENTO . "../../../licitacao/classes/mapeamento/TLicitacaoComissao.class.php");
        $obMapeamento   = new TLicitacaoComissao();
        $this->rsRecordSet    = new Recordset;

        $stFiltro = " WHERE comissao.cod_tipo_comissao = 4";

        $obMapeamento->recuperaRelacionamento( $this->rsRecordSet,$stFiltro,' ORDER BY comissao.cod_comissao');

        parent::ISelectComissao();

        //altera label
        $this->setRotulo( 'Equipe de Apoio' );

    }
}
?>

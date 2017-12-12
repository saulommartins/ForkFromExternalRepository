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
    * Componenente de Select dos centro de custos que o usuario tem permissão
    * Data de Criação: 27/02/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.07
*/

/*
$Log$
Revision 1.3  2006/07/06 14:04:38  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:09:20  diego

*/

include_once ( CLA_SELECT );

class ISelectCentroCustoUsuario extends Select
{
    public function ISelectCentroCustoUsuario()
    {
        parent::Select();

        include_once(CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoCentroDeCustos.class.php");
        $obRegra                = new RAlmoxarifadoPermissaoCentroDeCustos();
        $rsRecordSet            = new Recordset;

        $obRegra->obRCGMPessoaFisica->setNumCgm(Sessao::read('numCgm'));
        $obRegra->listarRelacionados($rsRecordSet);
        if ($rsRecordSet->getNumLinhas() == 1 )
           $inCodCentroCustoPadrao = $rsRecordSet->getCampo('cod_centro');

        $this->setRotulo            ("Centro de Custo"                          );
        $this->setTitle             ("Selecione o centro de custo"              );
        $this->setName              ("inCodCentroCusto"                         );
        $this->setNull              (true                                       );
        $this->setCampoID           ("cod_centro"                               );
        $this->addOption            ("","Selecione"                             );
        $this->setCampoDesc         ("[cod_centro] - [descricao]"               );
        $this->preencheCombo        ($rsRecordSet                               );
        $this->setValue             ($inCodCentroCustoPadrao                    );
    }

    public function setCodCentroCusto($inCodCentroCusto)
    {
        $this->inCodCentroCusto = $inCodCentroCusto;
    }

    public function montaHTML()
    {
        $this->setValue             ($this->inCodCentroCusto                    );
        parent::montaHTML();
    }
}
?>

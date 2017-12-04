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
    * Classe geracaoArquivoExportacao

    * Data de Criação   : 31/01/2008

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once CLA_RECORDSET;

class GeracaoArquivoExportacao
{
    public $arDados;
    public $rsDados;
    public $obExportador;

    public function GeracaoArquivoExportacao()
    {
        $this->setDados     ( array() );
        $this->setExportador( null    );
        $this->setRecordSet ( null    );
    }

    public function setDados($arDados)
    {
        $this->arDados = $arDados;

        $arDadosAux = array();
        foreach ($arDados as $stChave => $stValor) {
            $arDadosAux[0][$stChave] = $stValor;
        }

        $rsDados = new RecordSet();
        $rsDados->preenche( $arDadosAux );

        $this->setRecordSet( $rsDados );
    }

    public function setRecordSet($rsDados) { $this->rsDados = $rsDados; }
    public function getRecordSet() { return $this->rsDados; }

    public function setExportador($obExportador) { $this->obExportador = $obExportador; }
    public function getExportador() { return $this->obExportador; }
}
